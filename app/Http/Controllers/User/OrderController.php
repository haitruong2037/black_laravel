<?php

namespace App\Http\Controllers\User;

use App\Exceptions\Api\BadRequestException;
use App\Exceptions\Api\InternalServerErrorException;
use App\Http\Controllers\Controller;
use App\Jobs\SendOrderCreatedEmail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\UserAddress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Store a newly created order in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\InternalServerErrorException
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $userId = Auth::user()->id;

            $userAddress = UserAddress::where('id', $request->address_id)->where('user_id', $userId)->first();
            if (!$userAddress) {
                return response()->json(['message' => 'User Address not found'], 404);
            }
            $cartItems = Cart::where('user_id', $userId)->get();
            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'Cart is empty'], 400);
            }
            $total = 0;
            $discount = 0;
            foreach ($cartItems as $item) {
                $product = $item->product;
                if ($item->quantity > $product->quantity) {
                    return response()->json(['message' => $product->name . ' are not available in sufficient quantity'], 400);
                }
                $total += $item->product->price * $item->quantity;
                if ($item->product->discount) {
                    $discount += ($item->product->discount) * $item->quantity;
                }
            }
            $total -= $discount;
            $order = Order::create([
                'user_id' => $userId,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending',
                'note' => $request->note,
            ]);
            $name = $userAddress->name;
            $address = $userAddress->address;
            $phone = $userAddress->phone;

            $order->orderAddress()->create([
                'order_id' => $order->id,
                'name' => $name,
                'address' => $address,
                'phone' => $phone,
            ]);
            foreach ($cartItems as $item) {
                $product = $item->product;
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'quantity' => $item->quantity,
                ]);

                $product->quantity -= $item->quantity;
                $product->save();
            }
            Cart::where('user_id', $userId)->delete();
            DB::commit();
            SendOrderCreatedEmail::dispatch($order);
            return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw new InternalServerErrorException();
        }
    }

    /**
     * Display the authenticated user's order history.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\InternalServerErrorException
     */
    public function orderHistory()
    {
        try {
            $user = Auth::user();
            $orderHistory = Order::with(['orderDetails', 'orderAddress', 'orderDetails.product:id,name'])
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(10);
            return response()->json($orderHistory, 200);
        } catch (Exception $e) {
            throw new InternalServerErrorException();
        }
    }

    /**
     * Cancel the specified order if it is in pending status.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\InternalServerErrorException
     */
    public function cancelOrder(Request $request)
    {
        $userId = Auth::user()->id;
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $userId)
            ->with('orderDetails.product')
            ->firstOrFail();
        if ($order->status !== 'pending') {
            throw new BadRequestException('Only pending orders can be canceled');
        }
        try {
            DB::beginTransaction();
            $order->update(['status' => 'canceled']);

            foreach ($order->orderDetails as $orderDetail) {
                $product = $orderDetail->product;
                $product->quantity += $orderDetail->quantity;
                $product->save();
            }

            DB::commit();
            return response()->json(['message' => 'Order canceled successfully'], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw new InternalServerErrorException();
        }
    }
}
