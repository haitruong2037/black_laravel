<?php

namespace App\Http\Controllers\User;

use App\Exceptions\Api\InternalServerErrorException;
use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Response authenticated user's profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewProfile()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     * @throws \App\Exceptions\Api\InternalServerErrorException
     */
    public function updateProfile(Request $request)
    {
        $userID = Auth::guard('api')->user()->id;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userID,
            'address' => 'required|string|max:255',
            'image' => 'image|max:5120',
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/|unique:users,phone,' . $userID,
        ]);

        try {
            DB::beginTransaction();

            $user = User::find($userID);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->address = $request->address;
            $user->phone = $request->phone;
    
            if($request->hasFile('image'))
            {
                $imageName = ImageHelper::saveImage($request->file('image'), 'images/users');
                $user->image = $imageName;
            }
    
            $user->save();

            DB::commit();
    
            return response()->json(['success' => 'Account updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();

            throw new InternalServerErrorException();
        }
    }
}
