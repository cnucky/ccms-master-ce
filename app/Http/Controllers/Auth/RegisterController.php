<?php

namespace App\Http\Controllers\Auth;

use App\Constants\AvailableCountryAndAreaCodes;
use App\Http\Controllers\SPA\ClientAreaSPAController;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers {
        register as preformRegister;
    }

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return ClientAreaSPAController::view();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:32',
            'email' => 'required|string|email|max:64|unique:users',
            'phone' => 'required|digits_between:11,16',
            'password' => 'required|string|min:6|confirmed',
            'country' => function ($attribute, $value, $fail) {
                if (!array_key_exists($value, AvailableCountryAndAreaCodes::CODES))
                    $fail(':attribute is invalid');
            }
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
            'status' => User::STATUS_NORMAL,
            'country' => $data['country'],
            'user_quota_id' => 1,
        ]);
    }

    public function register(Request $request)
    {
        $response = $this->preformRegister($request);

        if ($request->expectsJson()) {
            return ["result" => true, "redirect" => $this->redirectPath()];
        }

        return $response;
    }
}
