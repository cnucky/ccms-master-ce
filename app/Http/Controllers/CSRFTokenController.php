<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CSRFTokenController extends Controller
{
    public function __invoke(Request $request)
    {
        return ["result" => true, "token" => $request->session()->token()];
    }
}
