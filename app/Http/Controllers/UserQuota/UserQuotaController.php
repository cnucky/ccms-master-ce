<?php

namespace App\Http\Controllers\UserQuota;

use App\Constants\GlobalErrorCode;
use App\UserQuota;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use YunInternet\CCMSCommon\Constants\Constants;

class UserQuotaController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:index," . UserQuota::class)->only([
            "edit",
            "show",
        ]);
        $this->middleware("can:create," . UserQuota::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . UserQuota::class)->only([
            "update",
        ]);
        $this->middleware("can:delete," . UserQuota::class)->only([
            "massDestroy",
            "destroy",
        ]);
    }

    public function show(Request $request, UserQuota $userQuota)
    {
        return ["result" => true, "data" => [
            "userQuota" => $userQuota,
        ]];
    }

    public function store(Request $request)
    {
        $this->storeValidate($request);
        $userQuota = UserQuota::query()->create($this->retrieveValues($request));
        return ["result" => true, "userQuota" => $userQuota];
    }

    public function update(Request $request, UserQuota $userQuota)
    {
        $this->storeValidate($request, $userQuota);
        $values = $this->retrieveValues($request);
        if ($userQuota->id === 1) {
            $values["auto_upgrade_after_net_income_more_than"] = 0;
        }
        $userQuota->update($values);
        return $this->show($request, $userQuota);
    }

    public function destroy(UserQuota $userQuota)
    {
        if ($userQuota->id === 1)
            return ["result" => false];
        DB::transaction(function () use ($userQuota) {
            $userQuota->users()->update([
                "user_quota_id" => 1,
            ]);
            $userQuota->delete();
        });
        return ["result" => true];
    }

    private function storeValidate(Request $request, UserQuota $except = null)
    {

        $nameValidateRule = Rule::unique("user_quotas", "name");
        if (!is_null($except)) {
            $nameValidateRule->ignore($except->id);
        }

        $uint32ValidateRule = "nullable|integer|min:0|max:" . Constants::UINT32_MAX;

        $this->validate($request, [
            "name" => [
                "required",
                $nameValidateRule
            ],
            "description" => "nullable|max:255",
            "max_instance" => $uint32ValidateRule,
            "max_storage_capacity_in_gib_unit" => $uint32ValidateRule,
            "max_elastic_ipv4_block" => $uint32ValidateRule,
            "max_elastic_ipv6_block" => $uint32ValidateRule,
            "auto_upgrade_after_net_income_more_than" => "nullable|integer|min:". ($except && $except->id === 1 ? "0" : "1") ."|max:" . Constants::UINT32_MAX,
        ]);
    }

    private function retrieveValues(Request $request)
    {
        return [
            "name" => $request->name,
            "description" => $request->description,
            "max_instance" => $request->max_instance,
            "max_storage_capacity_in_gib_unit" => $request->max_storage_capacity_in_gib_unit,
            "max_elastic_ipv4_block" => $request->max_elastic_ipv4_block,
            "max_elastic_ipv6_block" => $request->max_elastic_ipv6_block,
            "auto_upgrade_after_net_income_more_than" => $request->auto_upgrade_after_net_income_more_than,
        ];
    }
}
