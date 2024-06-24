<?php

namespace App\Http\Controllers\User;

use App\Exceptions\Api\InternalServerErrorException;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Retrieve products with optional filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Validate request data
        $request->validate([
            'category' => 'array',
            'category.*' => 'integer',
            'min_price' => 'numeric|min:0',
            'max_price' => 'numeric|min:0|gte:min_price',
            'search' => 'string'
        ], [
            'min_price.min' => 'The min price must be at least :min.',
            'max_price.gte' => 'The max price must be greater than or equal to the min price.',
        ]);

        try {
            $query = Product::select('id', 'name', 'price', 'discount', 'hot')
                ->where('status', 1);

            if (!empty($request->category) && count($request->category) > 0) {
                $query->whereIn('category_id', $request->input('category'));
            }

            if (!empty($request->min_price)) {
                $query->where('price', '>=', $request->input('min_price'));
            }

            if (!empty($request->max_price)) {
                $query->where('price', '<=', $request->input('max_price'));
            }

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->input('search') . '%');
            }

            $products = $query->orderBy('products.id', 'desc')->paginate(12);

            return response()->json($products);
        } catch (\Exception $e) {
            throw new InternalServerErrorException;
        }
    }

    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::with(['category', 'productImages' => function ($query) {
            $query->orderBy('default', 'desc');
        }])
            ->where('status', 1)
            ->findOrFail($id);

        try {
            $product->view += 1;
            $product->save();

            return response()->json($product);
        } catch (\Exception $e) {
            throw new InternalServerErrorException;
        }
    }

    /**
     * Display the releated product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function relatedProducts($id)
    {
        $product = Product::where('id', $id)->findOrFail($id);

        try {
            $relatedProducts = Product::select('id', 'name', 'price', 'discount', 'hot')
                ->where('id', '!=', $id)->where('category_id', $product->category_id)
                ->where('status', 1)->inRandomOrder()
                ->limit(4)->get();

            return response()->json($relatedProducts);
        } catch (\Exception $e) {
            throw new InternalServerErrorException;
        }
    }
}
