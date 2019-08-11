<?php

namespace App\Http\Controllers\PublicImage;

use App\ComputeInstance;
use App\ImageCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublicImageController extends Controller
{
    public function listUsableByInstance(ComputeInstance $computeInstance)
    {
        return ["result" => true, "imageCategories" => ImageCategory::query()->orderByDesc("order")->with("images")->get()];
    }
}
