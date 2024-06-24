<?php

namespace App\Http\Controllers\User;

use App\Exceptions\Api\InternalServerErrorException;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    /**
     * Retrieve all wishlist items for the authenticated user.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $userId = Auth::id();
            $wishlist = Wishlist::select('wishlists.id', 'wishlists.user_id', 'wishlists.product_id')
                ->with(['product' => function ($query) {
                    $query->select('id', 'name', 'price', 'discount');
                }])
                ->where('user_id', $userId)
                ->join('products', 'products.id', '=', 'wishlists.product_id')
                ->where('products.status', 1)
                ->paginate(12);
            return response()->json(['message' => 'Get Wishlist Successfully', 'data' => $wishlist], 200);
        } catch (Exception $e) {
            throw new InternalServerErrorException;
        }
    }
    /**
     * Store a newly created wishlist item.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $productId = $request->product_id;
        $checkProduct = Product::findOrFail($productId);
        try {
            $request->validate([
                'product_id' => 'required|integer'
            ]);

            $userId = Auth::id();

            if ($checkProduct->status !==  1) {
                return response()->json(['message' => 'Product is disabled'], 403);
            }

            $existingWishlistItem = Wishlist::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();
            if ($existingWishlistItem) {
                return response()->json(['message' => 'Product already in wishlist'], 400);
            }

            $wishlist = new Wishlist();
            $wishlist->user_id = $userId;
            $wishlist->product_id = $productId;
            $wishlist->save();
            return response()->json(['message' => 'Product added to wishlist successfully'], 201);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new InternalServerErrorException;
        }
    }
    /**
     * Remove a product from the user's wishlist.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($product_id)
    {
        try {
            $userId = Auth::id();
            $wishlistItem = Wishlist::where('user_id', $userId)
                ->where('product_id', $product_id)
                ->first();
            if (!$wishlistItem) {
                return response()->json(['error' => 'Wishlist item not found'], 404);
            }

            $wishlistItem->delete();
            return response()->json(['message' => 'Deleted wishlist item successfully']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new InternalServerErrorException;
        }
    }
}
