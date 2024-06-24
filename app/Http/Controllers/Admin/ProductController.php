<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $categories = Category::orderBy('name', 'ASC')->select('id', 'name')->get();

        $query = Product::with(['category', 'defaultImage'])->orderBy('id', 'desc');

        if(!empty($request->category)){
            $query->where('category_id', $request->input('category'));
        }
        
        if($request->has('status') && $request->status != null){
            $query->where('status', $request->input('status'));
        }

        $products = $query->paginate(10);

        $page = request()->get('page', 1);

        return view('pages.product.index', compact('products', 'categories', 'page'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->select('id', 'name')->get();
        
        return view('pages/product/create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductRequest $request)
    {
        try {
            DB::beginTransaction();

            $product = new Product;
            $product->name = $request->name;
            $product->price = $request->price;
            $product->discount = $request->discount;
            $product->quantity = $request->quantity;
            $product->description = $request->description;
            $product->hot = $request->hot ? true : false;   
            $product->status = $request->status;
            $product->category_id = $request->category_id;
            $product->admin_id = Auth::guard('admin')->user()->id;
            $product->save();

            // Upload main image
            if ($request->hasFile('image')) {
                $this->uploadImage($request, $product->id, true);
            }

            // Upload additional images
            if ($request->hasFile('additional_images')) {
                $this->uploadImage($request, $product->id, false);
            }

            DB::commit();

            return redirect()->back()->with('success','Product created successfully');
        } catch (\Exception $e ) {
            DB::rollBack();
            \Log::error($e->getMessage());

            return redirect()->back()->with('error', 'Failed to create product');
        }
    }

    /**
     * Display the specified product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $id)
    {
        $product = Product::with(['category','productImages' => function ($query) {
            $query->orderBy('default', 'DESC');
        } ])->findOrFail($id);

        $orderCount = OrderDetail::where('product_id', $id)
            ->whereHas('order', function ($query) {
                $query->where('status', '!=', 'canceled');
            })->sum('quantity');
        $wishlistCount = $product->wishlists()->count();
        $cartQuantity = $product->carts()->sum('quantity');

        $dataAnalytics = [
            'orderCount' => $orderCount,
            'wishlistCount' => $wishlistCount,
            'cartQuantity' => $cartQuantity
        ];
        
        $categories = Category::orderBy('name', 'ASC')->select('id', 'name')->get();
        return view('pages.product.edit', compact('product','categories', 'dataAnalytics'));
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        try {
            DB::beginTransaction();

            $product->name = $request->name;
            $product->price = $request->price;
            $product->discount = $request->discount;    
            $product->quantity = $request->quantity;
            $product->description = $request->description;
            $product->hot = $request->hot ? true : false;
            $product->status = $request->status;
            $product->category_id = $request->category_id;

            $product->save();

            // Upload main image
            if ($request->hasFile('image')) {
                ProductImage::where('product_id', $id)->update(['default' => 0]);

                $this->uploadImage($request, $product->id, true);
            }

            // Upload additional images
            if ($request->hasFile('additional_images')) {
                $this->uploadImage($request, $product->id, false);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Product updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to edit product');
        }
    }

    /**
     * Remove the specified product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {   
        try {
            $product = Product::findOrFail($id);

            $product->delete();

            return back()->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Error delete products: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete Product!');
        }
    }

    /**
     * Remove a product's additional image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyImage(Request $request, $productId , $id)
    {
        try {
            $image = ProductImage::findOrFail($id);

            $countImage = ProductImage::where('product_id', $productId)->where('id', '!=', $id)->count();

            if ($countImage == 0) {
                return back()->with('error', 'Product must have at least 1 product image!');
            }
            $image->delete();

            ImageHelper::deleteImage($image->file_name, 'images/products/' . $image->product_id);

            return back()->with('success', 'Image deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Error delete image: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete image!');
        }
    }

    /**
     * Set main image for product.
     *
     * @param int $productId The ID of product.
     * @param int $id The ID of image to set as main.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setMainImage($productId, $id)
    {
        $image = ProductImage::findOrFail($id);

        try {
            DB::beginTransaction();

            ProductImage::where('product_id', $productId)->update(['default'=> 0]);

            $image->default = 1;
            $image->save();

            DB::commit();

            return back()->with('success', 'Set main image successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error set main image: ' . $e->getMessage());
            return back()->with('error', 'Failed to set main image!');
        }
    }

    /**
     * Function to handle upload product's images
     * 
     * @param \Illuminate\Http\Request 
     * @param  int $productId
     * @param boolean $isMainImage
     * 
     * @return void
     */
    private function uploadImage($request, $productId, $isMainImage)
    {
        $images = $isMainImage ? [$request->file('image')] : $request->file('additional_images');

        foreach ($images as $image) {
            $imageName = ImageHelper::saveImage($image, 'images/products/' . $productId);

            ProductImage::create([
                'product_id' => $productId,
                'file_name' => $imageName,
                'default' => $isMainImage ? 1 : 0,
            ]);
        }
    }
}
