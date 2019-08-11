<?php

namespace App\Http\Controllers\SPA;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminAreaSPAController extends Controller
{
    public static function view()
    {
        return view('AdminArea.Desktop.adminarea');
    }

    public function __invoke()
    {
        return self::view();
    }
}
