<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Log;

class AdminController extends Controller
{
    /**
     * Display a listing of the admins.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $admins = Admin::orderBy('created_at', 'desc')->paginate(10);
        $page = request()->get('page', 1);

        return view('pages.admin.index', compact('admins', 'page'));
    }

    /**
     * Show the form for creating a new admin.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('pages.admin.create');
    }

    /**
     * Store a newly created admin in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|between:2,255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|confirmed|min:6',
        ], [
            'email.unique' => 'This email is already used by another admin!'
        ]);

        try {
            Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return back()->with('success', 'Admin created successfully');
        } catch (\Exception $e) {
            Log::error('Error sending email to user: ' . $e->getMessage());

            return back()->withErrors('error', 'Failed to create Admin!');
        }
    }

    /**
     * Show the form for editing admin.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $admin = Admin::findOrFail($id);

        return view('pages.admin.edit', compact('admin'));
    }

    /**
     * Update the admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $request->validate([
            'name' => 'required|string|between:2,255',
            'email' => 'required|string|email|max:255|unique:admins,email,' .$id,
        ], [
            'email.unique' => 'This email is already used by another admin'
        ]);

        try {
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->save();

            return back()->with('success', 'Admin updated successfully');
        }
        catch (\Exception $e) {
            Log::error('Error update admin: ' . $e->getMessage());

            return back()->with('error', 'Failed to update Admin!');
        }
    }

    /**
     * Destroy the admin.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($id == Auth::guard('admin')->user()->id) {
            return back()->with('error', 'Do not delete yourself!');
        }

        $admin = Admin::findOrFail($id);

        $admin->delete();

        return back()->with('success', 'Admin updated successfully');
    }
}
