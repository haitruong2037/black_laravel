<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class CommentFilter extends Component
{
    public $categories = [];
    public $selectedCategory;
    public $products = [];
    public $selectedProduct;

    public function mount()
    {
        $this->categories = Category::all();
        $this->products = collect();

        if (!empty(request()->input('category'))) {
            $this->setSelectedCategory(request()->input('category'));
        }

        if (!empty(request()->input('product'))) {
            $this->setSelectedProduct(request()->input('product'));
        }
    }

    /**
     * Sets the selected category ID and updates the filtered products.
     *
     * @param int $categoryId The ID of the selected category.
     *
     * @return void
     */
    public function setSelectedCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->products = Product::select('id', 'name')->where('category_id', $categoryId)->get();
    }

    /**
     * Sets the selected product ID.
     *
     * @param int $productId The ID of the selected product.
     *
     * @return void
     */
    public function setSelectedProduct($productId)
    {
        $this->selectedProduct = $productId;
    }

    public function render()
    {
        return view('livewire.comment-filter', [
            'categories' => $this->categories,
            'products' => $this->products,
        ]);
    }
}
