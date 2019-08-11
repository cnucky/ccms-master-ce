<?php

namespace App\Http\Controllers\AdminAPI\PublicISO;

use App\Http\Controllers\AdminAPI\ImageCommonValidator;
use App\Http\Controllers\ModelControllers\MassDestroyable;
use App\PublicISO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublicISOController extends Controller
{
    use ImageCommonValidator;

    use MassDestroyable;

    protected $categoryTable = "public_iso_categories";

    public function __construct()
    {
        $this->middleware("can:index," . PublicISO::class)->only([
            "edit",
            "show",
        ]);
        $this->middleware("can:create," . PublicISO::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . PublicISO::class)->only([
            "update",
        ]);
        $this->middleware("can:delete," . PublicISO::class)->only([
            "massDestroy",
            "destroy",
        ]);
    }

    public function store(Request $request)
    {
        $this->storeValidate($request, "public_isos");
        $values = $this->retrieveValues($request);

        return ["result" => true, "item" => PublicISO::query()->create($values)];
    }

    public function update(Request $request, PublicISO $publicISO)
    {
        $this->storeValidate($request, "public_isos", $publicISO);
        $values = $this->retrieveValues($request);
        $publicISO->update($values);

        return ["result" => true, "item" => $publicISO];
    }

    public function destroy(PublicISO $publicISO)
    {
        $publicISO->delete();

        return ["result" => true];
    }

    protected function modelQuery()
    {
        return PublicISO::query();
    }
}
