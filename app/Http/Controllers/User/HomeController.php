<?php

namespace App\Http\Controllers\User;

use App\Exceptions\Api\InternalServerErrorException;
use App\Http\Controllers\Controller;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Display data for home page, listing of the latest, discounted, and hot products.
     * 
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\InternalServerErrorException
     */
    public function index() {
        try {
            $latestProducts = Product::select('id','name', 'price', 'discount', 'hot')->where('status', 1)->orderBy('created_at', 'desc')->take(4)->get();
            $discountProducts = Product::select('id','name', 'price', 'discount', 'hot')->where('status', 1)->whereNotNull('discount')->orderBy('discount', 'desc')->take(4)->get();
            $hotProducts = Product::select('id','name', 'price', 'discount', 'hot')->where('status', 1)->where('hot', 1)->inRandomOrder()->take(4)->get();

            $data = [
                'latestProducts' => $latestProducts,
                'discountProducts' => $discountProducts,
                'hotProducts' => $hotProducts
            ];
    
            return response()->json($data);
        } catch (\Exception $e) {
            throw new InternalServerErrorException();
        }
    }
}
