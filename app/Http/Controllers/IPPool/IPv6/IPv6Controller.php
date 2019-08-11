<?php

namespace App\Http\Controllers\IPPool\IPv6;

use App\Constants\Constants;
use App\Constants\IPPool\TypeCode;
use App\Http\Controllers\IPPool\IPPoolMiddleware;
use App\Http\Controllers\ModelControllers\MassDestroyable;
use App\IPPool\IPv6;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use YunInternet\PHPIPCalculator\Calculator\IPv6 as IPv6Calculator;

class IPv6Controller extends Controller
{
    use MassDestroyable;

    use IPPoolMiddleware;

    public function store(Request $request)
    {
        $values = $this->storeValidate($request);
        return ["result" => true, "pool" => IPv6::query()->create($values)];
    }

    public function update(Request $request, IPv6 $IPv6)
    {
        $values = $this->storeValidate($request);

        $subnetNetworkBits = $values["subnet_network_bits"];
        $totalSubnet = $values["total_subnet"];
        unset($values["subnet_network_bits"]);
        unset($values["total_subnet"]);

        $originFirstUsableIP = $IPv6->human_readable_first_usable_ip;

        DB::begintransaction();
        try {
            $IPv6->update($values);
            IPv6::query()->where("id", $IPv6->id)->whereNotExists(function ($builder) use ($IPv6) {
                $builder->select(DB::raw(1))
                    ->from("ipv6_assignments")
                    ->where("pool_id", $IPv6->id)
                    ->whereNotNull("ipv6_assignments.user_id");
            })->update(["subnet_network_bits" => $subnetNetworkBits, "total_subnet" => $totalSubnet]);
            // Refresh human readable ip in assignments
            $IPv6->refreshAssignment();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return ["result" => true, "pool" => $IPv6];
    }

    public function show(IPv6 $IPv6)
    {
        $model = IPv6::query()->withCount("assigned")->where("id", $IPv6->id)->firstOrFail();
        return ["result" => true, "pool" => $model, "assigned_count" => $model->assigned_count];
    }

    public function destroy(IPv6 $IPv6)
    {
        $IPv6->delete();
        return ["result" => true];
    }

    public function validatePreview(Request $request)
    {
        $values = $this->storeValidate($request, $isAligend);
        return ["result" => true, "isAligned" => $isAligend, "values" => $values];
    }

    protected function modelQuery()
    {
        return IPv6::query();
    }

    private function storeValidate(Request $request, &$isAligned = false)
    {
        $this->validate($request, [
            "human_readable_first_usable_ip" => "required|ipv6",
            "human_readable_last_usable_ip" => "required|ipv6",
            "network_bits" => "required|integer|min:0|max:128",
            "human_readable_gateway" => "nullable|ipv6",
            "subnet_network_bits" => "required|integer|min:0|max:128",
            "description" => "nullable|max:191",
            "type" => ["required", Rule::in([TypeCode::TYPE_PUBLIC_NETWORK, TypeCode::TYPE_PRIVATE_NETWORK])],
            "status" => ["required", Rule::in([0, 1])],
            "price_per_hour" => Constants::DECIMAL_8_4_PRICE_VALIDATE_RULE,
        ]);

        $calculableFirstUsableIP = IPv6Calculator::humanReadable2Calculable($request->human_readable_first_usable_ip);
        $calculableLastUsableIP = IPv6Calculator::humanReadable2Calculable($request->human_readable_last_usable_ip);
        if (IPv6Calculator::compare($calculableFirstUsableIP, $calculableLastUsableIP) === 1)
            throw ValidationException::withMessages(["first_usable_ip" => "首可用IP位于末可用IP后"]);

        $calculableGateway = [null, null, null, null];
        if ($request->human_readable_gateway)
            $calculableGateway = IPv6Calculator::humanReadable2Calculable($request->human_readable_gateway);

        // Align to $request->subnet_network_bits
        $firstUsableGeneratedIPv6Calculator = new IPv6Calculator($calculableFirstUsableIP, $request->subnet_network_bits);
        // Align first to next subnet if need
        $firstUsableIP = $calculableFirstUsableIP;
        if ($firstUsableGeneratedIPv6Calculator->getFirstAddress() !== $firstUsableIP) {
            $isAligned = true;
            $firstUsableGeneratedIPv6Calculator = $firstUsableGeneratedIPv6Calculator->getSubnetAfter();
            $firstUsableIP = $firstUsableGeneratedIPv6Calculator->getFirstAddress();
        }
        $lastUsableGeneratedIPv6Calculator = new IPv6Calculator($calculableLastUsableIP, $request->subnet_network_bits);
        // Align last to pre subnet if need
        $lastUsableIP = $calculableLastUsableIP;
        if ($lastUsableGeneratedIPv6Calculator->getLastAddress() !== $lastUsableIP) {
            $isAligned = true;
            $lastUsableGeneratedIPv6Calculator = $lastUsableGeneratedIPv6Calculator->getSubnetBefore();
            $lastUsableIP = $lastUsableGeneratedIPv6Calculator->getLastAddress();
        }

        if (IPv6Calculator::compare($firstUsableIP, $lastUsableIP) === 1)
            throw ValidationException::withMessages(["subnet_network_bits" => "当前的可用IP范围无法划分出至少一个/" . $request->subnet_network_bits]);

        $totalSubnetUint128 = $firstUsableGeneratedIPv6Calculator->distanceTo($lastUsableGeneratedIPv6Calculator);
        $totalSubnet = $totalSubnetUint128[3];
        if ($totalSubnetUint128[0] || $totalSubnetUint128[1] || $totalSubnetUint128[2])
            $totalSubnet = 0xFFFFFFFF;
        if ($totalSubnet !== 0xFFFFFFFF)
            ++$totalSubnet;

        return [
            "description" => $request->description,
            "first_usable_ip_part_0" => $firstUsableIP[0],
            "first_usable_ip_part_1" => $firstUsableIP[1],
            "first_usable_ip_part_2" => $firstUsableIP[2],
            "first_usable_ip_part_3" => $firstUsableIP[3],
            "human_readable_first_usable_ip" => IPv6Calculator::calculable2HumanReadable($firstUsableIP),
            "last_usable_ip_part_0" => $lastUsableIP[0],
            "last_usable_ip_part_1" => $lastUsableIP[1],
            "last_usable_ip_part_2" => $lastUsableIP[2],
            "last_usable_ip_part_3" => $lastUsableIP[3],
            "human_readable_last_usable_ip" => IPv6Calculator::calculable2HumanReadable($lastUsableIP),
            "network_bits" => $request->network_bits,
            "gateway_part_0" => $calculableGateway[0],
            "gateway_part_1" => $calculableGateway[1],
            "gateway_part_2" => $calculableGateway[2],
            "gateway_part_3" => $calculableGateway[3],
            "human_readable_gateway" => $request->human_readable_gateway,
            "subnet_network_bits" => $request->subnet_network_bits,
            "total_subnet" => $totalSubnet,
            "assign_for_new_instance" => boolval($request->assign_for_new_instance),
            "assign_for_extra_ip" => boolval($request->assign_for_extra_ip),
            "type" => $request->type,
            "status" => $request->status,
            "price_per_hour" => is_null($request->price_per_hour) ? "0" : $request->price_per_hour,
        ];
    }
}
