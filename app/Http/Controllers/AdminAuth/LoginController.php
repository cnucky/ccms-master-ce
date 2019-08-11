<?php

namespace App\Http\Controllers\AdminAuth;

use App\Constants\StatusCode;
use App\Http\Controllers\SPA\AdminAreaSPAController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers {
        sendLoginResponse as preformSendLoginResponse;
        logout as preFormLogout;
    }

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm()
    {
        return AdminAreaSPAController::view();
    }

    public function redirectTo()
    {
        return route("admin.dashboard", [], false);
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
            return ["result" => true, "redirect" => "/login", "token" => csrf_token()];
        return $response;
    }

    /**
     * @inheritDoc
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->status !== StatusCode::STATUS_NORMAL) {
            $this->preFormLogout($request);
            return $this->sendFailedLoginResponse($request);
        }
    }


    protected function guard()
    {
        return Auth::guard("admin");
    }
}
