<?php

namespace App\Http\Controllers\PublicFloppy;

use App\PublicFloppyCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function __invoke()
    {
        return ["result" => true, "categories" => PublicFloppyCategory::query()->with("publicFloppies")->get()];
    }
}
