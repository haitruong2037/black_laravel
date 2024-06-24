<?php

namespace App\Http\Controllers\User;

use App\Exceptions\Api\BadRequestException;
use App\Exceptions\Api\InternalServerErrorException;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Response list of the user's carts
     * 
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\BadRequestException
     * @throws \App\Exceptions\Api\InternalServerErrorException
     */
    public function index()
    {
        try {
            $userID = Auth::user()->id;
            $carts = Cart::with(['product' => function ($query) {
                    $query->select('id', 'name', 'price', 'discount', 'category_id')
                        ->with('category:id,name');}])
                        ->whereHas('product', function ($query) {$query->where('status', 1);})
                        ->where('user_id', $userID)
                        ->orderBy('id', 'desc')
                        ->get();

            return response()->json($carts, 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching user addresses: ', ['error' => $e->getMessage()]);

            throw new InternalServerErrorException;
        }
    }

    /**
     * Store or update cart for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\BadRequestException
     * @throws \App\Exceptions\Api\InternalServerErrorException
     */
    public function storeOrUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1',
            'product_id' => 'required|exists:products,id',
        ],
        [
            'quantity.min' => 'Quantity must be at least :min.',
            'product_id.required' => 'Please select product to add to cart.',
            'product_id.exists' => 'The selected product not found.',
        ]);

        $userID = Auth::user()->id;

        $data = array_merge($validatedData, ['user_id' => $userID]);

        // Check product is available
        $product = Product::findOrFail($data['product_id']);
        if ($product->status != 1) {
            throw new BadRequestException('The selected product is not available');
        }

        $checkExistingCartItem = Cart::where('user_id', $userID)->where('product_id', $data['product_id'])->first();

        $newQuantity = $checkExistingCartItem ? $checkExistingCartItem->quantity + $data['quantity'] : $validatedData['quantity']; 
        
        if ($newQuantity > $product->quantity) {
            throw new BadRequestException('Maximum number of products has been reached');
        }
        
        try {
            if ($checkExistingCartItem) {
                $checkExistingCartItem->update(['quantity' => $newQuantity]);
            } else {
                Cart::create($data);
            }

            return response()->json(['message' => $checkExistingCartItem ? 'Product updated to cart successfully!' : 'Product added to cart successfully!'], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating user address: ', ['error' => $e]);

            throw new InternalServerErrorException();
        }
    }

    /**
     * Update cart item 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\BadRequestException
     * @throws \App\Exceptions\Api\InternalServerErrorException
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ], [
            'quantity.min' => 'Quantity must be at least :min.',
        ]);

        $userID = Auth::user()->id;

        $cart = Cart::where('id', $id)->where('user_id', $userID)->firstOrFail();
        $productQuantity = $cart->product->quantity;
        $newQuantity = $request->quantity;
        
        if ($newQuantity > $productQuantity) {
            throw new BadRequestException('Maximum number of products has been reached');
        }

        try {
            $cart->update(['quantity' => $newQuantity]);

            return response()->json(['message' => 'Product updated in cart successf ully.'], 201);

        } catch (\Exception $e) {
            throw new InternalServerErrorException();
        }
    }

    /**
     * Remove the product form item cart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\InternalServerErrorException
     */
    public function delele($id)
    {
        $userID = Auth::user()->id;
        $cart = Cart::where('id', $id)->where('user_id', $userID)->firstOrFail();

        try {
            $cart->delete();
            return response()->json(['message' => 'Product removed from cart successfully.'], 200);

        } catch (\Exception $e) {
            throw new InternalServerErrorException();
        }
    }
}
