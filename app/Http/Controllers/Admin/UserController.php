<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a paginated list of users.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $users = User::paginate(10);
            $page = request()->get('page', 1);
            return view('pages.user.index', compact('users'));
        } catch (Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return view('pages.user.index', ['error' => 'Failed to fetch users']);
        }
    }

    /**
     * Show the form for creating a new user.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('pages.user.create');
    }

    /**
     * Store a newly created user in the database.
     * 
     * @param UserRequest $request
     */
    public function store(UserRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $imageName = ImageHelper::saveImage($request->file('image'), 'images/users');
                $data['image'] = $imageName;
            }
            User::create($data);
            DB::commit();
            return redirect()->back()->with('success', __('Create users successfully'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', ('Create users failed'));
        }
    }

    /**
     * Display the specified user.
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return view('pages.user.edit', compact('user'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'User not found');
        }
    }

    /**
     * Update the specified user.
     * 
     * @param UserRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(UserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            DB::beginTransaction();
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $imageName = ImageHelper::saveImage($request->file('image'), 'images/users');
                $user->image = $imageName;
            }
            $user->update($data);
            DB::commit();
            return redirect()->back()->with('success', __('Update users successfully'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', ('Update users failed'));
        }
    }

    /**
     * Display the user details including orders and wishlist.
     *
     * @param int $id The ID of the user.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function userDetail($id)
    {
        $userDetail = User::with(['order', 'wishlist.product:id,name'])->findOrFail($id);
        try {
            $totalOrders = $userDetail->order->count();
            $totalWishlists = $userDetail->wishlist->count();
            return view('pages.user.detail', compact('userDetail', 'totalOrders', 'totalWishlists'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', ('Failed to retrieve user details.'));
        }
    }

    /**
     * Delete a user.
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            $imageName = $user->image;
            $folder = 'images/users';

            ImageHelper::deleteImage($imageName, $folder);

            $user->delete();

            return back()->with('success', __('Delete User successfully'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', ('Delete users failed'));
        }
    }
}
