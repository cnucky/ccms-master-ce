<?php

namespace App\Http\Controllers;

use App\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:index," . Currency::class)->only([
            "index",
            "edit",
            "show",
        ]);
        $this->middleware("can:create," . Currency::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . Currency::class)->only([
            "update",
        ]);
        $this->middleware("can:delete," . Currency::class)->only([
            "massDestroy",
            "destroy",
        ]);
    }

    public function index()
    {
        return ["result" => true, "currencies" => Currency::query()->get()];
    }

    public function edit(Request $request, Currency $currency)
    {
        return ["result" => true, "currency" => $currency];
    }

    public function store(Request $request)
    {
        $this->commonValidate($request);
        $values = $this->retrieveValues($request);
        $currency = Currency::query()->create($values);
        return ["result" => true, "currency" => $currency];
    }

    public function update(Request $request, Currency $currency)
    {
        $this->commonValidate($request, $currency);
        $values = $this->retrieveValues($request);
        if ($currency->id === 1) {
            unset($values["exchange_rate"]);
        }
        $currency->update($values);
        return ["result" => true, "currency" => $currency];
    }

    public function destroy(Currency $currency)
    {
        if ($currency->id === 1)
            return ["result" => false];
        $currency->delete();
        return ["result" => true];
    }

    private function commonValidate(Request $request, Currency $except = null)
    {
        $codeUniqueRule = Rule::unique("currencies", "code");
        if (!is_null($except)) {
            $codeUniqueRule->ignore($except->id);
        }

        $this->validate($request, [
            "currency_code" => [
                "required",
                "min:3",
                "max:3",
                $codeUniqueRule,
            ],
            "prefix" => "nullable|max:8",
            "suffix" => "nullable|max:8",
            "exchange_rate" => [
                "required",
                "min:0.000001",
                "regex:/^[0-9]{1,6}(\.[0-9]{0,6}){0,1}$/"
            ],
        ]);
    }

    private function retrieveValues(Request $request)
    {
        return [
            "code" => $request->currency_code,
            "prefix" => is_null($request->prefix) ? "" : $request->prefix,
            "suffix" => is_null($request->suffix) ? "" : $request->suffix,
            "exchange_rate" => $request->exchange_rate,
        ];
    }
}
