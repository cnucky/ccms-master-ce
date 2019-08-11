<?php

namespace App\Http\Controllers\User;

use App\Constants\AvailableCountryAndAreaCodes;
use App\Constants\Constants;
use App\Constants\CreditRecordType;
use App\Constants\GlobalErrorCode;
use App\TrafficShareGroup;
use App\User;
use App\UserCreditRecord;
use App\UserQuota;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:index," . User::class)->only([
            "show",
            "consumption",
            "dailyConsumption",
            "monthlyConsumption",
        ]);
        $this->middleware("can:update," . User::class)->only([
            "update",
        ]);
        $this->middleware("can:create," . User::class)->only([
            "store",
        ]);
        $this->middleware("can:addCredit," . User::class)->only([
            "addCredit"
        ]);
        $this->middleware("can:removeCredit," . User::class)->only([
            "removeCredit"
        ]);
        $this->middleware("can:suspend," . User::class)->only([
            "suspend"
        ]);
        $this->middleware("can:unsuspend," . User::class)->only([
            "unsuspend"
        ]);
        $this->middleware("can:login," . User::class)->only([
            "login",
        ]);
    }

    public function show(Request $request, $user)
    {
        /**
         * @var User $userModel
         */
        $userModel = User::query()
            ->withCount([
                "instances",
                "localVolumes",
                "ipv4s",
                "ipv6s",
                "tickets",
            ])
            ->where("id", $user)
            ->first();

        return ["result" => true, "data" => [
            "user" => $userModel,
            "trafficShareGroups" => TrafficShareGroup::query()->get()->keyBy("id"),
            "currentMonthTrafficUsages" => $userModel->getUserCurrentMonthTrafficUsageAttribute(),
            "currentMonthTotalChargedAmount" => $userModel->getCurrentMonthTotalChargedAmountAttribute(),
            "availableUserQuotas" => UserQuota::query()->get(),
            "netIncoming" => $userModel->getNetIncomingAttribute(),
        ]];
    }

    public function login(Request $request, User $user)
    {
        Auth::guard("web")->login($user);
        return redirect(route("dashboard"))->header("Cache-Control", "no-cache, no-store, must-revalidate");
    }

    public function consumption(Request $request, User $user)
    {
        return [
            "result" => true,
            "data" => [
                "total_consumption" => $user->getTotalChargedAmountAttribute(),
                "total_consumption_group_by_type" => $user->getTotalChargedAmountGroupByTypeAttribute(),
            ],
        ];
    }

    public function dailyConsumption(Request $request, User $user)
    {
        return [
            "result" => true,
            "data" => [
                "total_consumption" => $user->getUserDailyChargedAmountAttribute(),
                "total_consumption_group_by_type" => $user->getUserDailyChargedAmountGroupByTypeAttribute(),
            ],
        ];
    }

    public function monthlyConsumption(Request $request, User $user)
    {
        return [
            "result" => true,
            "data" => [
                "total_consumption" => $user->getUserMonthlyChargedAmountAttribute(),
                "total_consumption_group_by_type" => $user->getUserMonthlyChargedAmountGroupByTypeAttribute(),
            ],
        ];

    }

    public function suspend(User $user)
    {
        $user->update([
            "status" => User::STATUS_SUSPENDED,
        ]);

        return ["result" => true];
    }

    public function unsuspend(User $user)
    {
        $user->update([
            "status" => User::STATUS_NORMAL,
        ]);

        return ["result" => true];
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required|string|max:32',
            'email' => ['required', 'string', 'email', 'max:64', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|digits_between:11,16',
            'country' => function ($attribute, $value, $fail) {
                if (!array_key_exists($value, AvailableCountryAndAreaCodes::CODES))
                    $fail(':attribute 不存在');
            },
            'status' => ["required", Rule::in([User::STATUS_PENDING, User::STATUS_NORMAL, User::STATUS_SUSPENDED])],
            'password' => 'nullable|string|min:6',
        ]);

        $values = [
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "country" => $request->country,
            "status" => $request->status,
            "disable_quota_auto_upgrade" => boolval($request->disable_quota_auto_upgrade),
        ];

        if ($request->password) {
            $values["password"] = bcrypt($request->password);
        }

        if ($request->disable_quota_auto_upgrade) {
            $this->validate($request, [
                "user_quota_id" => ["required", Rule::exists("user_quotas", "id")],
            ]);
            $values["user_quota_id"] = $request->user_quota_id;
        }

        $user->update($values);

        return ["result" => true, "user" => $user->refresh()];
    }

    public function addCredit(Request $request, User $user)
    {
        $this->validate($request, [
            "amount" => [
                "required",
                Constants::DECIMAL_13_4_PRICE_VALIDATE_RULE,
            ],
            "description" => [
                "nullable",
                "max:65535",
            ],
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->addCredit($request->amount);
            UserCreditRecord::query()->create([
                "user_id" => $user->id,
                "amount" => $request->amount,
                "type" => CreditRecordType::TYPE_ADD_CREDIT_MANUALLY,
                "description" => $request->description,
            ]);
        });

        return $this->returnUserCredit($user);
    }

    public function removeCredit(Request $request, User $user)
    {
        $this->validate($request, [
            "amount" => [
                "required",
                Constants::DECIMAL_13_4_PRICE_VALIDATE_RULE,
            ],
            "description" => [
                "nullable",
                "max:65535",
            ],
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->removeCredit($request->amount);
            UserCreditRecord::query()->create([
                "user_id" => $user->id,
                "amount" => "-" . $request->amount,
                "type" => CreditRecordType::TYPE_CHARGE_MANUALLY,
                "description" => $request->description,
            ]);
        });

        return $this->returnUserCredit($user);
    }

    public function destroy(User $user)
    {
        if ($user->instances()->count() || $user->localVolumes()->count() || $user->ipv4s()->count() || $user->ipv6s()->count())
            return ["result" => false, "errno" => GlobalErrorCode::RELEASE_ALL_RESOURCE_IS_REQUIRED];
        $user->delete();
        return ["result" => true];
    }

    private function returnUserCredit(User $user)
    {
        $user->refresh();
        return ["result" => true, "newCredit" => $user->credit];
    }
}
