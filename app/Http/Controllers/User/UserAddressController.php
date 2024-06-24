<?php

namespace App\Http\Controllers\User;

use App\Exceptions\Api\BadRequestException;
use App\Exceptions\Api\InternalServerErrorException;
use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserAddressController extends Controller
{
    /**
     * Response list of the user's addresses.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $userID = Auth::guard('api')->user()->id;
            $userAddresses = UserAddress::where('user_id', $userID)->get();
    
            return response()->json($userAddresses, 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching user addresses: ', ['error' => $e->getMessage()]);

            throw new InternalServerErrorException();
        }
    }

    /**
     * Store a new address for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,11}$/'],
            'default' => 'nullable|boolean',
        ],
        [
            'phone.regex' => 'The phone number must be 10 to 11 digits long and contain only numbers.',
        ]);

        
        $validatedData['default'] = $validatedData['default'] ?? false;

        $userID = Auth::guard('api')->user()->id;
        $addressCount = UserAddress::where('user_id', $userID)->count();

        if ($addressCount >= 10) {
            throw new BadRequestException('You can only have up to 10 addresses.');
        }

        try {
            DB::beginTransaction();

            $data = array_merge($validatedData, ['user_id' => $userID]);

            if ($data['default']) {
                UserAddress::where('user_id', $userID)->update(['default' => false]);
            }

            UserAddress::create($data);
            DB::commit();

            return response()->json(['message' =>'Address successfully created'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
           \Log::error('Error creating user address: ', ['error' => $e]);

            throw new InternalServerErrorException();
        }
    }

    /**
     * Response the user's address.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $userID = Auth::guard('api')->user()->id;
        $userAddress = UserAddress::where('id', $id)->where('user_id', $userID)->firstOrFail();

        return response()->json($userAddress, 200);
    }

    /**
     * Update the address for user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,11}$/'],
            'default' => 'nullable|boolean',
        ],
        [
            'phone.regex' => 'The phone number must be 10 to 11 digits long and contain only numbers.',
        ]);

        $validatedData['default'] = $validatedData['default'] ?? false;

        $userID = Auth::guard('api')->user()->id;
        $userAddress = UserAddress::where('id', $id)->where('user_id', $userID)->firstOrFail();

        try {
            DB::beginTransaction();

            if ($validatedData['default']) {
                UserAddress::where('user_id', $userID)->update(['default' => false]);
            }

            $userAddress->update($validatedData);
            DB::commit();

            return response()->json(['message'=> 'Updated address successfully'],200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating user address: ', ['error' => $e]);

            throw new InternalServerErrorException();
        }
    }

    /**
     * Remove address for the user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $userID = Auth::guard('api')->user()->id;
        $addressCount = UserAddress::where('user_id', $userID)->count();
        
        if($addressCount <= 1) {
            throw new BadRequestException('You need to have at least 1 address!');
        }

        $userAddress = UserAddress::where('id', $id)->where('user_id', $userID)->firstOrFail();
        
        try {
            $userAddress->delete();
        } catch (\Exception $e) {
            throw new InternalServerErrorException();
        }

        return response()->json(['message' => 'Address deleted successfully'], 200);
    }
}
