<?php

namespace App\Http\Controllers\User;

use App\Exceptions\Api\InternalServerErrorException;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'data' => $categories
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new InternalServerErrorException;
        }
    }
}
