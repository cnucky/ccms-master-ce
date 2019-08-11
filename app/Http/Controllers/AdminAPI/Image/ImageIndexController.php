<?php

namespace App\Http\Controllers\AdminAPI\Image;

use App\Http\Controllers\Controller;
use App\Image;
use App\ImageCategory;
use Illuminate\Http\Request;

class ImageIndexController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:index," . Image::class);
    }

    public function __invoke(Request $request)
    {
        return ["result" => true, "categories" => ImageCategory::query()->with("images")->get()];
    }
}
