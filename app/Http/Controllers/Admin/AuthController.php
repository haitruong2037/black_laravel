<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmalResetPassword;
use App\Mail\ResetPasswordMail;
use App\Models\Admin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Str;
use Validator;

class AuthController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showLoginView()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request){
        if (Auth::guard('admin')->attempt($request->only('email', 'password') )) {
            return redirect()->intended(route('admin.index'));
        }
        
        return back()->withErrors(['error' => 'Login infomation is incorrect']);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    /**
     * Create a new admin user instance after a valid registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ],[
            'email.unique' => 'This email address is already in use'
        ]);

        $admin = Admin::create(
            array_merge(
                $validator->validated(),
                ['password' => Hash::make($request->password)]
            )
        );

        return redirect()->route('admin')->with('success','Admin' .$admin->name. 'created successfully');
    }

    /**
     * Change the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassWord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|confirmed|min:6',
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->old_password, $admin->password)) {
            return back()->withErrors(['error' => 'The old password is incorrect']);
        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return back()->with('success', 'User successfully changed password');
    }

    /**
     * Show the form for requesting a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function forgotPasswordCreate() {
        return view('auth.forgot-password');
    }

    /**
     * Handle the sending of the password reset link email.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
        ], [
            'email.exists' => 'Email not found',
        ]);

        $user = Admin::where('email', $request->email)->first();

        $token = Password::createToken($user);

        $actionURL = url(route('admin.password.reset', ['token' => $token, 'email' => $user->email]));

        SendEmalResetPassword::dispatch($actionURL, $request->email);


        return back()->with('success','Password reset email sent');
    }

    /**
     * Show the form for resetting the password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function resetPasswordCreate(Request $request)
    {
        return view('auth.recover-password', ['token' => $request->token, 'email' => $request->email]);
    }

    /**
     * Handle the password reset.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPasswordStore(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Admin $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login_view')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
