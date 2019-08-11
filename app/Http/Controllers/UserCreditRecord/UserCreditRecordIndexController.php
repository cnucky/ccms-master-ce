<?php

namespace App\Http\Controllers\UserCreditRecord;

use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\User;
use App\UserCreditRecord;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserCreditRecordIndexController extends FilterableIndexController
{
    protected $sortableColumns = [
        "id" => true,
        "type" => true,
        "amount" => true,
        "created_at" => true,
    ];

    public function index(Request $request)
    {
        return ["result" => true, "records" => $this->paginate($request, $this->makeBasicBuilder($request))];
    }

    public function indexByUser(Request $request, User $user)
    {
        return ["result" => true, "records" => $this->paginate($request,
            $this->makeVeryBasicBuilder($request)
                ->where("user_id", $user->id)
        )];
    }

    public function exportIndex(Request $request)
    {
        $result = $this->makeBasicBuilder($request)->get();
        return $this->result2XLS($request, $result);
    }

    public function exportByUser(Request $request, User $user)
    {
        return $this->result2XLS($request, $this->makeVeryBasicBuilder($request)->where("user_id", $user->id)->get());
    }

    private function result2XLS(Request $request, $result)
    {
        $pageHead = <<<EOF
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
</head>
<body>
EOF;
        $tableStart = <<<EOF
<table border="1">
<thead>
<tr>
    <th>ID</th>
    <th>类型</th>
    <th>相关对象ID</th>
    <th>日期</th>
    <th>金额</th>
    <th>描述</th>
</tr>
</thead>
<tbody>
EOF;

        $tbodyRows = "";
        foreach ($result as $record) {
            $tbodyRows .= sprintf(<<<EOF
<tr>
<td>%s</td>
<td>%s</td>
<td>%s</td>
<td>%s</td>
<td>%s</td>
<td>%s</td>
</tr>
EOF
                , $record->id, __("creditRecordType." . $record->type), $record->relative_object_id, $record->created_at . " " . config("app.timezone"), $record->amount, $record->description);

        }

        $fullPageContent = $pageHead . $tableStart . $tbodyRows . <<<EOF
</tbody>
</table>
</body>
</html>
EOF;

        return
            response($fullPageContent)
                ->header("Content-type", "application/vnd.ms-excel")
                ->header("Content-Disposition", "attachment; filename=" . $request->user()->id . "-" . time() . ".xls")
            ;
    }

    private function makeBasicBuilder(Request $request)
    {
        return $this->makeVeryBasicBuilder($request)->where("user_id", $request->user()->id);
    }

    private function makeVeryBasicBuilder(Request $request)
    {
        $builder = UserCreditRecord::query();
        if (!$request->sortKey) {
            $builder->orderByDesc("id");
        }
        if ($request->startDate) {
            $builder->where("created_at", ">=", $request->startDate);
        }
        if ($request->endDate) {
            $builder->where("created_at", "<=", $request->endDate);
        }
        if (!is_null($request->type) && $request->type != "-1") {
            $builder->where("type", $request->type);
        }

        return $builder;
    }
}
