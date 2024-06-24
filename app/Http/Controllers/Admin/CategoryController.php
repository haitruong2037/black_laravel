<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Validator;

class CategoryController extends Controller
{
    /** 
     * Display the category creation page.
     */
    public function create()
    {
        return view('pages.category.create');
    }

    /**
     * Store a newly created category in the database.
     * 
     * @param CategoryRequest $request
     * @throws Exception
     */
    public function store(CategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = [
                'name' => $request->input('name'),
            ];
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalName();
                $image->move(public_path('images/categories'), $imageName);
                $data['image'] = $imageName;
            }
            Category::create($data);
            DB::commit();
            return redirect()->back()->with('success', __('Create Category successfully'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Create Category Failed');
        }
    }

    /**
     * Display a listing of categories.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $categories = Category::paginate(9);
            $page = request()->get('page', 1);
            return view('pages.category.index', compact('categories', 'page'));
        } catch (Exception $e) {
            return view('pages.category.index', ['error' => 'Failed to retrieve categories']);
        }
    }

    /**
     * Display the specified category.
     * 
     * @param int $id
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return view('pages.category.edit', compact('category'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Category not found');
        }
    }

    /**
     * Update the specified category in the database.
     * 
     * @param CategoryRequest $request
     * @param int $id
     * @throws Exception
     */
    public function edit(CategoryRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $category = Category::findOrFail($id);
            $data = [
                'name' => $request->input('name')
            ];
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalName();
                $image->move(public_path('images/categories'), $imageName);
                $data['image'] = $imageName;
            }
            $category->update($data);
            DB::commit();
            return redirect()->back()->with('success', __('Update Category successfully'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', __('Update Category Failed'));
        }
    }

    /**
     * Remove the specified category from the database.
     * 
     * @param int $id
     * @throws Exception
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $category = Category::findOrFail($id);

            $checkProductExists = $category->product()->exists();
            if ($checkProductExists) {
                return redirect()->back()->with('error', 'The category cannot be deleted as it contains products.');
            }

            $category->delete();
            DB::commit();
            return back()->with('success', __('Delete Category successfully'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', __('Delete Category Failed'));
        }
    }
}
