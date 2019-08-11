<?php

namespace App\Http\Controllers\AdminAPI;

use App\Constants\AdminPermissions;
use App\Constants\AvailableSystemConfigurations;
use App\SystemConfiguration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SystemConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:" . AdminPermissions::SUPER);
    }

    public function show()
    {
        return [
            "result" => true,
            "systemConfigurations" => SystemConfiguration::query()->get()->pluck("value", "name"),
        ];
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            "AUTO_RELEASE_LC_USER_RESOURCE_AFTER" => "nullable|integer|min:0|max:65535",
        ]);
        SystemConfiguration::setValue(AvailableSystemConfigurations::SYSTEM_HOST, $request->{AvailableSystemConfigurations::SYSTEM_HOST});
        SystemConfiguration::setValue(AvailableSystemConfigurations::AUTO_RELEASE_LC_USER_RESOURCE_AFTER, $request->{AvailableSystemConfigurations::AUTO_RELEASE_LC_USER_RESOURCE_AFTER});
        return ["result" => true];
    }
}
