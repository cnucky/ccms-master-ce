<?php

namespace App\Http\Controllers\AdminAPI\PublicFloppy;

use App\Http\Controllers\AdminAPI\ImageCommonValidator;
use App\Http\Controllers\ModelControllers\MassDestroyable;
use App\PublicFloppy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublicFloppyController extends Controller
{
    use ImageCommonValidator;

    use MassDestroyable;

    protected $categoryTable = "public_floppy_categories";

    public function __construct()
    {
        $this->middleware("can:index," . PublicFloppy::class)->only([
            "edit",
            "show",
        ]);
        $this->middleware("can:create," . PublicFloppy::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . PublicFloppy::class)->only([
            "update",
        ]);
        $this->middleware("can:delete," . PublicFloppy::class)->only([
            "massDestroy",
            "destroy",
        ]);
    }


    public function store(Request $request)
    {
        $this->storeValidate($request, "public_floppies");
        $values = $this->retrieveValues($request);

        return ["result" => true, "item" => PublicFloppy::query()->create($values)];
    }

    public function update(Request $request, PublicFloppy $publicFloppy)
    {
        $this->storeValidate($request, "public_floppies", $publicFloppy);
        $values = $this->retrieveValues($request);
        $publicFloppy->update($values);

        return ["result" => true, "item" => $publicFloppy];
    }

    public function destroy(PublicFloppy $publicFloppy)
    {
        $publicFloppy->delete();

        return ["result" => true];
    }

    protected function modelQuery()
    {
        return PublicFloppy::query();
    }
}
