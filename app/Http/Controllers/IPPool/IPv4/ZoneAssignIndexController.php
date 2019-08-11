<?php

namespace App\Http\Controllers\IPPool\IPv4;

use App\Http\Controllers\IPPool\IPPoolUpdateMiddleware;
use App\IPPool\IPv4;
use App\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ZoneAssignIndexController extends Controller
{
    use IPPoolUpdateMiddleware;

    public function __invoke(Request $request, IPv4 $IPv4)
    {
        return ["result" => true, "assignments" => $IPv4->zoneAssignments()->with("region")->get()->keyBy("id"), "availableZones" => Zone::query()->with("region")->get()->keyBy("id")];
    }
}
