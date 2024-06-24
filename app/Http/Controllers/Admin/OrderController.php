<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a paginated listing of orders with optional filtering by status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */

    public function index(Request $request)
    {
        try {
            $query = Order::with(['user']);

            if ($request->has('status') && $request->status != "") {
                $status = $request->status;
                $query->where('status', $status);
            }

            $orders = $query->latest()->paginate(12);
            return view('pages.order.index', compact('orders'));
        } catch (Exception $e) {
            Log::error('Failed to get order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Get Order Failed');
        }
    }

    /**
     * Update the specified order's status.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $currentStatus = $order->status;
        $newStatus = $request->status;

        if (($currentStatus === 'canceled') ||
            ($currentStatus === 'delivered') ||
            ($currentStatus === 'shipping' && $newStatus != 'delivered') ||
            ($currentStatus === 'processing' && $newStatus === 'pending')
        ) {
            return redirect()->back()->with('error', 'Cannot change status to ' . $currentStatus . ' from ' . $newStatus);
        }
        try {
            DB::beginTransaction();
            if ($newStatus === 'canceled') {
                $orderDetails = $order->orderDetails;
                foreach ($orderDetails as $detail) {
                    $product = $detail->product;
                    $product->quantity += $detail->quantity;
                    $product->save();
                }
            }
            $order->status = $newStatus;
            $order->save();
            DB::commit();
            return redirect()->back()->with('success', __('Update status successfully'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Update status failed');
        }
    }

    /**
     * Display the details of an order and its address.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function detailOrder($id)
    {
        $order = Order::with('user:id,name', 'orderAddress')->findOrFail($id);
        try {
            $orderDetails = OrderDetail::with('product:id,name')->where('order_id', $id)->paginate(6);

            return view('pages.order.detail', compact('order', 'orderDetails'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to retrieve order details.');
        }
    }

    /**
     * Remove the specified order from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $order = Order::findOrFail($id);
            $order->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Delete order successfully');
        } catch (Exception $e) {
            Log::error('Failed to delete order: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Delete order failed');
        }
    }
}
