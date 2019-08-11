<?php

namespace App\Http\Controllers;

use App\Constants\AvailableCountryAndAreaCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function changeProfile(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:32',
            'email' => ['required', 'string', 'email', 'max:64', Rule::unique('users')->ignore($request->user()->id)],
            'phone' => 'required|digits_between:11,16',
            'country' => function ($attribute, $value, $fail) {
                if (!array_key_exists($value, AvailableCountryAndAreaCodes::CODES))
                    $fail(':attribute 不存在');
            }
        ]);

        $request->user()->update([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "country" => $request->country,
        ]);

        return ["result" => true, "user" => $request->user()];
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $request->user()->password))
            throw ValidationException::withMessages(["现密码错误"]);

        $request->user()->update([
            "password" => bcrypt($request->new_password),
        ]);

        return ["result" => true];
    }
}
