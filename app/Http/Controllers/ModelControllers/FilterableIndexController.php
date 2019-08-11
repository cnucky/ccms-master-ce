<?php
/**
 * Created by PhpStorm.
 * Date: 19-1-16
 * Time: 下午7:33
 */

namespace App\Http\Controllers\ModelControllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class FilterableIndexController extends Controller
{
    protected $sortableColumns = [];

    protected $equalSearchColumns = [];

    protected $leftMatchSearchColumns = [];

    protected $fulltextSearchColumns = [];

    protected $allowPrePageValue = [
        "15" => true,
        "30" => true,
        "50" => true,
        "100" => true,
    ];

    protected function paginate(Request $request, $query)
    {
        $this->retrieveItems($request, $query);

        $prePage = 15;
        if (array_key_exists($request->prePage, $this->allowPrePageValue))
            $prePage = $request->prePage;

        return $query->paginate($prePage);
    }

    protected function retrieveItems(Request $request, $query)
    {
        $this->search($request, $query);
        $this->sort($request, $query);
    }

    private function search(Request $request, $query)
    {
        if ($request->has("search")) {
            $query->where(function (Builder $builder) use ($request) {
                foreach ($this->equalSearchColumns as $column)
                    $builder->orWhere($column, "=", $request->search);
                foreach ($this->leftMatchSearchColumns as $column)
                    $builder->orWhere($column, "LIKE", $request->search . "%");
                foreach ($this->fulltextSearchColumns as $column)
                    $builder->orWhere($column, "LIKE", "%". $request->search ."%");
            });
        }

        return $query;
    }

    private function sort(Request $request, $query)
    {
        if ($request->has("sortKey") && $request->has("isAsc")) {
            if (array_key_exists($request->sortKey, $this->sortableColumns)) {
                if (is_callable($this->sortableColumns[$request->sortKey])) {
                    call_user_func($this->sortableColumns[$request->sortKey], $query, $request->sortKey, $request->isAsc);
                } else {
                    if ($request->isAsc)
                        $query->orderBy($request->sortKey);
                    else
                        $query->orderByDesc($request->sortKey);
                }
            }
        }

        return $query;
    }
}