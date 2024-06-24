<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with counts of users, admins, products, categories, and orders.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $counts = [
            'countUsers' => User::count(),
            'countAdmins' => Admin::count(),
            'countProducts' => Product::count(),
            'countCategories' => Category::count(),
            'countOrders' => Order::count()
        ];

        return view('pages.dashboard.index', compact('counts'));
    }
}
