<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {


        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'items' => []], 220);
        }

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $user_uuid = auth()->guard()->id();
            $this->clearLoginAttempts($request);
            $user = User::query()->find($user_uuid);
            $user->setAttribute('token', $user->createToken('user_api')->plainTextToken);
            return response()->json(['message' => 'success', 'items' => $user]);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function validator(array $data)
    {
        $rules = [
            $this->username() => 'required',
            'password' => 'required|string',
        ];

        return Validator::make($data, $rules);
    }

    public function username()
    {
        return 'email';
    }

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );
        return response()->json(['message' => __('auth.throttle', ['seconds' => $seconds]), 'items' => []], 220);
    }

    protected function attemptLogin(Request $request)
    {
        return auth()->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    protected function credentials(\Illuminate\Http\Request $request)
    {
//        if (filter_var($request->get('mobile'), FILTER_VALIDATE_EMAIL)) {
//            return ['email' => $request->get('mobile'), 'password' => $request->get('password'), 'deleted_at' => null];
//        }
        return ['email' => $request->get('email'), 'password' => $request->get('password'), 'deleted_at' => null];
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return response()->json(['message' => 'failed', 'items' => []], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'success', 'items' => []], 200);
    }

    protected function guard()
    {
        return Auth::guard();
    }

}
