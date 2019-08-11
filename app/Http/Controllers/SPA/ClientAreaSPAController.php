<?php

namespace App\Http\Controllers\SPA;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientAreaSPAController extends Controller
{
    public static function view()
    {
        return view('ClientArea.Desktop.clientarea');
    }

    public function __invoke()
    {
        return self::view();
    }
}
