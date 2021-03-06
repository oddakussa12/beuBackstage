<?php

namespace App\Http\Controllers\Auth;

use ActionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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
    protected $redirectTo = '/backstage';
    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout()
    {
        ActionLog::createActionLog('sign out' , 'sign out success');
        $this->guard()->logout();

//        $request->session()->invalidate();

        return redirect($this->redirectTo);
    }

    public function showLoginForm(Request $request)
    {
        $redirect = $request->input('origin' , '/backstage');
        return view('admin.login' , compact('redirect'));
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        throw ValidationException::withMessages([
            'email' => [Lang::get('auth.throttle', ['seconds' => $seconds])],
        ])->status(423);
    }

    public function username()
    {
        return 'admin_email';
    }

    protected function credentials(Request $request)
    {

        return array(
            $this->username() => $request->input('email'),
            'password' => $request->input('password')
        );
    }

    public function validateCredentials(UserContract $user, array $credentials)
    {
        return $this->hasher->check(
            $credentials['password'], $user->getAuthPassword()
        );
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);
        ActionLog::createActionLog('sign in' , 'sign in success');
        return response()->json(array('message'=>trans('auth.successed')));
    }


}


