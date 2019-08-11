<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SPA\ClientAreaSPAController;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        return ClientAreaSPAController::view();
    }

    /**
     * @inheritDoc
     */
    protected function sendResetLinkResponse($response)
    {
        return ["result" => true, "message" => trans($response)];
    }

    /**
     * @inheritDoc
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return ["result" => false, "message" => trans($response)];
    }


}
