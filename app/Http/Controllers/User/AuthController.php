<?php

namespace App\Http\Controllers\User;
use App\Exceptions\Api\BadRequestException;
use App\Exceptions\Api\ValidationException;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmailVerificationMail;
use App\Jobs\SendEmalResetPassword;
use App\Mail\ResetPasswordMail;
use App\Models\UserAddress;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\MessageBag;
use Mail;
use Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if (!$token = Auth::guard('api')->attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Login infomation is incorect'], 401);
        }

        $user = Auth::guard('api')->user();

        if (!$user->hasVerifiedEmail()) {
            throw new BadRequestException('Please verify your email before logging in');
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'address' => 'required|string|',
            'phone' => 'required|string|min:10|max:11|unique:users',
        ],[
            'email.unique' => 'This email address is already in use',
            'phone.unique' => 'This phone number is already in use',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        try {
            $userData = array_merge(
                $validator->validated(),
                ['password' => Hash::make($request->password), 
                'email_verification_hash' => md5(uniqid()),
                ]
            );
    
            $user = User::create($userData);
    
            $addressData = [
                'user_id' => $user->id,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'default' => true,
            ];
    
            UserAddress::create($addressData);
    
            SendEmailVerificationMail::dispatch($user);

            return response()->json([
                'message' => 'User successfully registered. Please check email to confirm registration',
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to register'], 500);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $token = Auth::guard('api')->refresh();
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['message' => 'Token is invalid'], 401);
        }

        $user = auth()->user();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => $user
        ]);
    }

    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * Change the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassWord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $userId = Auth::guard('api')->user()->id;

        $user = User::find($userId);
            
        if (!Hash::check($request->old_password, $user->password)) {
           return response()->json(['message' => 'The old password is incorrect'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'User successfully changed password',
        ], 201);
    }

    /**
     * Send a reset password link email to the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ],[
            'email.exists' => 'Email not found',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Please enter correct email',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $user = User::where('email', $request->email)->first();

            $token = Password::createToken($user);

            $actionURL = url(env('FRONT_END_URL', 'http://localhost:5173') . '/auth/reset-password/'.$token.'/'.$user->email);

            SendEmalResetPassword::dispatch($actionURL, $request->email);

            return response()->json(['message' => 'Password reset email sent']);
        }catch (\Exception $e) {
            \Log::error('Error sending email to admins: ' . $e->getMessage());
            return response()->json(['message'=> 'An error occurred while sending the reset email'], 400);
        }
    }

    /**
     * Handle the password reset.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPasswordStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ],[
            'email.exists' => 'Email not found',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json([
                'message' => 'Password reset failed',
                'email' => [__($status)]
            ], 400);
    }

    /**
     * Create a new JWT token.
     *
     * @param  string  $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => $user
        ]);
    }

    /**
     * Send email verification link to user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Api\ValidationException
     */
    public function sendMailVerify(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if($user->hasVerifiedEmail()) {
            $errors = new MessageBag([
                'email' => ['Your email has been previously verified.'],
            ]);

            throw new ValidationException($errors);
        }

        $user->email_verification_hash = md5(uniqid());
        $user->save();
    
        SendEmailVerificationMail::dispatch($user);

        return response()->json(['message' => 'Verified email has been sent successfully, Please check your email.']);
    }

    /**
     * Verify the email using id and hash
     * 
     * @param int $id
     * @param string $hash
     * @return \Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\Api\BadRequestException
     */
    public function verifyMail($id, $hash){

        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            if($user->email_verification_hash === $hash){
                $user->email_verified_at = now();
                $user->email_verification_hash = null;
                $user->save();

                return response()->json(['message' => 'Email has been successfully verified.']);
            }else {
                throw new BadRequestException("The verification link is not valid.");
            }
        } else {
            throw new BadRequestException("Your email has been previously verified.");
        }
    }
}
