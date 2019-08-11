<?php

namespace App\Http\Controllers\PublicISO;

use App\PublicISOCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function __invoke()
    {
        return ["result" => true, "categories" => PublicISOCategory::query()->orderByDesc("order")->with("publicIsos")->get()];
    }
}
