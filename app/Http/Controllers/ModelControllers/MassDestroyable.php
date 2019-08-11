<?php
/**
 * Created by PhpStorm.
 * Date: 19-1-16
 * Time: 下午8:34
 */

namespace App\Http\Controllers\ModelControllers;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait MassDestroyable
{
    public function massDestroy(Request $request)
    {
        if ($request->has("items") && is_array($items = $request->items)) {
            $deletedItemCount = $this->modelQuery()->whereIn($this->keyColumn(), $items)->delete();
        } else {
            $deletedItemCount = 0;
            $items = [];
        }

        return ["result" => true, "items" => $items, "deletedItemCount" => $deletedItemCount];
    }

    protected function keyColumn()
    {
        return "id";
    }

    /**
     * @return Builder
     */
    abstract protected function modelQuery();
}