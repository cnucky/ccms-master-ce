<?php

namespace App\Http\Controllers\IPPool\IPv4;

use App\Constants\Constants;
use App\Constants\GlobalErrorCode;
use App\Constants\IPPool\TypeCode;
use App\Http\Controllers\IPPool\IPPool;
use App\Http\Controllers\IPPool\IPPoolMiddleware;
use App\Http\Controllers\ModelControllers\MassDestroyable;
use App\IPPool\IPv4;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use YunInternet\PHPIPCalculator\Calculator\IPv4 as IPv4Calculator;
use YunInternet\PHPIPCalculator\Contract\IPCalculator;

class IPv4Controller extends Controller
{
    use IPPool;

    use MassDestroyable;

    use IPPoolMiddleware;

    public function store(Request $request)
    {
        $values = $this->storeValidate($request);
        // Check ip duplicated for public network ip
        if ($request->type == TypeCode::TYPE_PUBLIC_NETWORK) {
            $checkResult = $this->firstAndLastCheck($values["first_usable_ip"], $values["last_usable_ip"]);
            if ($checkResult !== true)
                return $checkResult;
        }
        /**
         * @var IPv4 $pool
         */
        $pool = IPv4::query()->create($values);
        $pool->prepare();
        return ["result" => true, "pool" => $pool];
    }

    public function update(Request $request, IPv4 $IPv4)
    {
        /**
         * @var IPCalculator $firstCalculator
         * @var IPCalculator $lastCalculator
         */
        $values = $this->storeValidate($request, $isAligned, $firstCalculator, $lastCalculator);
        $checkResult = $this->firstAndLastCheck($values["first_usable_ip"], $values["last_usable_ip"], $IPv4->id);
        if ($checkResult !== true)
            return $checkResult;

        $distance = $firstCalculator->distanceTo($lastCalculator);
        $result = $this->isLastUsableIPBeforeAnyAssignedIP($IPv4, $distance);
        if ($result !== true)
            return $result;

        $subnetNetworkBits = $values["subnet_network_bits"];
        $totalSubnet = $values["total_subnet"];
        unset($values["subnet_network_bits"]);
        unset($values["total_subnet"]);

        $originFirstUsableIP = $IPv4->human_readable_first_usable_ip;

        DB::beginTransaction();
        try {
            $IPv4->update($values);
            IPv4::query()->where("id", $IPv4->id)->whereNotExists(function ($builder) use ($IPv4) {
                $builder->select(DB::raw(1))
                    ->from("ipv4_assignments")
                    ->where("pool_id", $IPv4->id)
                    ->whereNotNull("ipv4_assignments.user_id");
            })->update(["subnet_network_bits" => $subnetNetworkBits, "total_subnet" => $totalSubnet]);

            // Refresh human readable ip in assignments
            $IPv4->refreshAssignment();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return ["result" => true, "pool" => $IPv4];
    }

    public function show(IPv4 $IPv4)
    {
        $model = IPv4::query()->withCount("assigned")->where("id", $IPv4->id)->firstOrFail();
        return ["result" => true, "pool" => $model, "assigned_count" => $model->assigned_count];
    }

    public function destroy(IPv4 $IPv4)
    {
        $IPv4->delete();
        return ["result" => true];
    }

    public function validatePreview(Request $request)
    {
        $values = $this->storeValidate($request, $isAligend);
        return ["result" => true, "isAligned" => $isAligend, "values" => $values];
    }

    protected function modelQuery()
    {
        return IPv4::query();
    }

    private function storeValidate(Request $request, &$isAligned = false, &$firstUsableGeneratedIPv4Calculator = null, &$lastUsableGeneratedIPv4Calculator = null)
    {
        $this->validate($request, [
            "human_readable_first_usable_ip" => "required|ipv4",
            "human_readable_last_usable_ip" => "required|ipv4",
            "network_bits" => "required|integer|min:0|max:32",
            "human_readable_gateway" => "nullable|ipv4",
            "subnet_network_bits" => "required|integer|min:0|max:32",
            "description" => "nullable|max:191",
            "type" => ["required", Rule::in([TypeCode::TYPE_PUBLIC_NETWORK, TypeCode::TYPE_PRIVATE_NETWORK])],
            "status" => ["required", Rule::in([0, 1])],
            "price_per_hour" => Constants::DECIMAL_8_4_PRICE_VALIDATE_RULE,
        ]);

        $calculableFirstUsableIP = IPv4Calculator::humanReadable2Calculable($request->human_readable_first_usable_ip);
        $calculableLastUsableIP = IPv4Calculator::humanReadable2Calculable($request->human_readable_last_usable_ip);
        if (IPv4Calculator::compare($calculableFirstUsableIP, $calculableLastUsableIP) === 1)
            throw ValidationException::withMessages(["first_usable_ip" => "首可用IP位于末可用IP后"]);

        $calculableGateway = null;
        if ($request->human_readable_gateway)
            $calculableGateway = IPv4Calculator::humanReadable2Calculable($request->human_readable_gateway);

        // Align to $request->subnet_network_bits
        $firstUsableGeneratedIPv4Calculator = new IPv4Calculator($calculableFirstUsableIP, $request->subnet_network_bits);
        // Align first to next subnet if need
        $firstUsableIP = $calculableFirstUsableIP;
        if ($firstUsableGeneratedIPv4Calculator->getFirstAddress() !== $firstUsableIP) {
            $isAligned = true;
            $firstUsableGeneratedIPv4Calculator = $firstUsableGeneratedIPv4Calculator->getSubnetAfter();
            $firstUsableIP = $firstUsableGeneratedIPv4Calculator->getFirstAddress();
        }
        $lastUsableGeneratedIPv4Calculator = new IPv4Calculator($calculableLastUsableIP, $request->subnet_network_bits);
        // Align last to pre subnet if need
        $lastUsableIP = $calculableLastUsableIP;
        if ($lastUsableGeneratedIPv4Calculator->getLastAddress() !== $lastUsableIP) {
            $isAligned = true;
            $lastUsableGeneratedIPv4Calculator = $lastUsableGeneratedIPv4Calculator->getSubnetBefore();
            $lastUsableIP = $lastUsableGeneratedIPv4Calculator->getLastAddress();
        }

        if (IPv4Calculator::compare($firstUsableIP, $lastUsableIP) === 1)
            throw ValidationException::withMessages(["subnet_network_bits" => "当前的可用IP范围无法划分出至少一个/" . $request->subnet_network_bits]);

        return [
            "description" => $request->description,
            "first_usable_ip" => $firstUsableIP,
            "human_readable_first_usable_ip" => IPv4Calculator::calculable2HumanReadable($firstUsableIP),
            "last_usable_ip" => $lastUsableIP,
            "human_readable_last_usable_ip" => IPv4Calculator::calculable2HumanReadable($lastUsableIP),
            "network_bits" => $request->network_bits,
            "gateway" => $calculableGateway,
            "human_readable_gateway" => $request->human_readable_gateway,
            "subnet_network_bits" => $request->subnet_network_bits,
            "total_subnet" => $firstUsableGeneratedIPv4Calculator->distanceTo($lastUsableGeneratedIPv4Calculator) + 1,
            "assign_for_new_instance" => boolval($request->assign_for_new_instance),
            "assign_for_extra_ip" => boolval($request->assign_for_extra_ip),
            "type" => $request->type,
            "status" => $request->status,
            "price_per_hour" => is_null($request->price_per_hour) ? "0" : $request->price_per_hour,
        ];
    }

    private function firstAndLastCheck($first, $last, $except = null)
    {
        $firstCheckResult = $this->inRangeCheck($first, $except);
        if ($firstCheckResult)
            return ["result" => false, "errno" => GlobalErrorCode::FIRST_IP_IN_ANOTHER_POOL_RANGE, "pool" => $firstCheckResult];
        $lastCheckResult = $this->inRangeCheck($last, $except);
        if ($lastCheckResult)
            return ["result" => false, "errno" => GlobalErrorCode::LAST_IP_IN_ANOTHER_POOL_RANGE, "pool" => $lastCheckResult];
        return true;
    }

    private function inRangeCheck($ip, $except = null)
    {
        $query = IPv4::query();
        if ($except)
            $query->where("id", "!=", $except);
        return $query
            ->where("first_usable_ip", "<=", $ip)
            ->where("last_usable_ip", ">=", $ip)
            ->first()
        ;
    }
}
