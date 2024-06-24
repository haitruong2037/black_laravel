<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the form for updating the password.
     *
     * @return \Illuminate\View\View
     */
    public function updatePasswordCreate()
    {
        return view('pages.profile.update-password');
    }

    /**
     * Handle the password update.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePasswordStore(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|confirmed|min:6',
        ]);

        $adminId = Auth::guard('admin')->user()->id;

        $admin = Admin::find($adminId);

        if (!Hash::check($request->old_password, $admin->password)) {
            return back()->withErrors(['password' => 'The old password is incorrect']);
        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return back()->with('success', 'User successfully changed password');
    }

    /**
     * Display the authenticated admin's profile.
     *
     * @return \Illuminate\View\View
     */
    public function viewProfile()
    {
        $profile = Admin::find(Auth::guard('admin')->user()->id);

        return view('pages.profile.update-profile', compact('profile'));
    }

    /**
     * Update the authenticated admin's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $adminID = Auth::guard('admin')->user()->id;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,'. $adminID,
        ]);

        try {
            $admin = Admin::find($adminID);
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->save();

            return back()->with('success', 'Account updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update your account.']);
        }
    }
}
