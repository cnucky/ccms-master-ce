<?php

namespace App\Http\Controllers\PaymentModule;

use App\Constants\GlobalErrorCode;
use App\Currency;
use App\Module\Contract\PaymentModule\DedicatedNotifiable;
use App\Module\Exception\ModuleNotFoundException;
use App\Module\PaymentModuleLoader;
use App\PaymentModule;
use App\PaymentModuleSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:operate," . PaymentModule::class);
    }

    public function availableModules()
    {
        return ["result" => true, "availableModules" => PaymentModuleLoader::scan()];
    }

    public function index()
    {
        $basicPaymentModuleList = DB::table("payment_modules")->select("basic_payment_module")->groupBy("basic_payment_module")->pluck("basic_payment_module");
        $basicPaymentModules = [];
        foreach ($basicPaymentModuleList as $name) {
            try {
                $basicPaymentModules[$name] = new PaymentModuleLoader($name);
            } catch (ModuleNotFoundException $moduleNotFoundException) {}
        }

        return ["result" => true, "basicPaymentModules" => $basicPaymentModules, "paymentModules" => PaymentModule::query()->get()];
    }

    public function edit(PaymentModule $paymentModule)
    {
        try {
            $basicPaymentModule = $paymentModule->getPaymentModuleLoader();
        } catch (ModuleNotFoundException $e) {
            $basicPaymentModule = null;
        }

        $paymentModule->currency;

        return [
            "result" => true,
            "availableCurrencies" => Currency::query()->get(),
            "basicPaymentModule" => $basicPaymentModule,
            "paymentModuleSettings" => $paymentModule->settings()->pluck("value", "name"),
            "paymentModule" => $paymentModule,
            "isDedicatedNotifiable" => $basicPaymentModule ? $basicPaymentModule->getReflectionClass()->implementsInterface(DedicatedNotifiable::class) : false,
            "notifyURLPrefix" => env("PAYMENT_MODULE_NOTIFY_SCHEMA_AND_HTTP_HOST", null),
        ];
    }

    public function store(Request $request)
    {
        $this->storeValidate($request);
        $this->validateBasicPaymentModule($request);

        $values = $this->retrieveCommonValues($request);
        $values["basic_payment_module"] = $request->basic_payment_module;

        return ["result" => true, "paymentModule" => PaymentModule::query()->create($values)];
    }

    public function update(Request $request, PaymentModule $paymentModule)
    {
        $this->storeValidate($request, $paymentModule);
        $paymentModule->update($this->retrieveCommonValues($request));
        return ["result" => true, "paymentModule" => $paymentModule];
    }

    public function updateModuleSetting(Request $request, PaymentModule $paymentModule)
    {
        $values = [];
        $moduleLoader = $paymentModule->getPaymentModuleLoader();
        foreach ($moduleLoader->getAvailableSettings() as $name => $setting) {
            $values[] = [
                "payment_module_id" => $paymentModule->id,
                "name" => $name,
                "value" => $request->get($name),
            ];
        }

        DB::transaction(function () use ($paymentModule, &$values) {
            $paymentModule->settings()->delete();
            PaymentModuleSetting::query()->insert($values);
        });

        return ["result" => true];
    }

    public function destroy(PaymentModule $paymentModule)
    {
        $paymentModule->delete();
        return ["result" => true];
    }

    private function storeValidate(Request $request, PaymentModule $exceptId = null)
    {
        $uniqueRule = Rule::unique("payment_modules", "name");
        if ($exceptId) {
            $uniqueRule->ignore($exceptId->id);
        }

        $this->validate($request, [
            "name" => [
                "required",
                "max:32",
                $uniqueRule,
            ],
            "currency_id" => [
                "nullable",
                "exists:currencies,id"
            ],
            "status" => [
                "nullable",
                Rule::in([0, 1]),
            ],
            "order" => [
                "nullable",
                "integer",
                "min:-32768",
                "max:32767",
            ],
        ]);
    }

    private function validateBasicPaymentModule(Request $request)
    {
        $this->validate($request, [
            "basic_payment_module" => [
                "regex:/^[a-zA-Z0-9_]{1,255}$/",
            ],
        ]);

        try {
            new PaymentModuleLoader($request->basic_payment_module);
        } catch (ModuleNotFoundException $e) {
            throw ValidationException::withMessages(["basic_payment_module" => ":attribute not exists"]);
        }
    }

    private function retrieveCommonValues(Request $request)
    {
        return [
            "name" => $request->name,
            "currency_id" => $request->currency_id,
            "status" => is_null($request->status) ? 0 : $request->status,
            "order" => is_null($request->order) ? 0 : $request->order,
        ];
    }
}
