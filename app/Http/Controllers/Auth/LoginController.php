<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SPA\ClientAreaSPAController;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

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

    use AuthenticatesUsers {
        sendLoginResponse as preformSendLoginResponse;
        logout as preFormLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return ClientAreaSPAController::view();
    }

    protected function sendLoginResponse(Request $request)
    {
        /**
         * @var RedirectResponse $response
         */
        $response = $this->preformSendLoginResponse($request);

        if ($request->expectsJson())
            return ["result" => true, "redirect" => $this->redirectPath(), "token" => csrf_token()];
        else
            return $response;
    }

    protected function logout(Request $request)
    {
        $response = $this->preFormLogout($request);

        if ($request->expectsJson())
            return ["result" => true, "redirect" => "/", "token" => csrf_token()];
        return $response;
    }

    /**
     * @inheritDoc
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->status === User::STATUS_SUSPENDED) {
            $this->preFormLogout($request);
            return $this->sendFailedLoginResponse($request);
        }
    }
}
