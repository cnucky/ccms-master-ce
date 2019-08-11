<?php

namespace App\Http\Controllers\TrafficShareGroup;

use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\TrafficShareGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TrafficShareGroupIndexController extends FilterableIndexController
{
    public function __construct()
    {
        $this->middleware("can:index," . TrafficShareGroup::class);
    }

    public function __invoke()
    {
        return ["result" => true, "trafficShareGroups" => TrafficShareGroup::query()->get()];
    }
}
