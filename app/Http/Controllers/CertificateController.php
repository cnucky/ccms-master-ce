<?php

namespace App\Http\Controllers;

use App\Certificate;
use App\Http\Controllers\ModelControllers\FilterableIndexController;
use App\Http\Controllers\ModelControllers\MassDestroyable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CertificateController extends FilterableIndexController
{
    use MassDestroyable;

    protected $sortableColumns = [
        "id" => true,
        "valid_to" => true,
        "updated_at" => true,
    ];

    protected $equalSearchColumns = [
        "id",
    ];

    protected $leftMatchSearchColumns = [
        "name",
        "fingerprint"
    ];

    public function __construct()
    {
        $this->middleware("can:index," . Certificate::class)->only([
            "edit",
            "index",
            "listCertificates",
        ]);
        $this->middleware("can:create," . Certificate::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . Certificate::class)->only([
            "update",
        ]);
        $this->middleware("can:delete," . Certificate::class)->only([
            "destroy",
            "massDestroy",
        ]);
    }

    public function index(Request $request)
    {
        return ["result" => true, "certificates" => $this->paginate($request, Certificate::query())];
    }

    public function listCertificates(Request $request)
    {
        return ["result" => true, "certificates"  => Certificate::query()->get()];
    }

    public function edit(Request $request, Certificate $certificate)
    {
        return ["result" => true, "certificate" => $certificate];
    }

    public function store(Request $request)
    {
        $this->storeValidate($request);
        $this->validateCertificate($request);
        return ["result" => true, "certificate" => Certificate::query()->create($this->retrieveValues($request))];
    }

    public function update(Request $request, Certificate $certificate)
    {
        $this->storeValidate($request, $certificate);
        if (!is_null($request->get("certificate"))) {
            $this->validateCertificate($request);
            $certificate->update($this->retrieveValues($request, $certificate));
        } else {
            $certificate->update(["name" => $request->name]);
        }

        return ["result" => true, "certificate" => $certificate];
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();
        return ["result" => true];
    }

    /**
     * @inheritDoc
     */
    protected function modelQuery()
    {
        return Certificate::query();
    }

    private function storeValidate(Request $request, Certificate $except = null)
    {
        $nameRule = Rule::unique("certificates", "name");
        if (!is_null($except))
            $nameRule->ignore($except->id);
        $this->validate($request, [
            "name" => [
                "required",
                "max:191",
                $nameRule,
            ],
        ]);
    }

    private function validateCertificate(Request $request)
    {
        if (!openssl_x509_check_private_key($request->get("certificate"), $request->privateKey)) {
            $this->invalidCertificate();
        }
    }

    private function retrieveValues(Request $request, Certificate $except = null)
    {
        $certificateParsedResult = openssl_x509_parse($request->certificate);
        if ($certificateParsedResult === false)
            $this->invalidCertificate();

        $fingerprint = openssl_x509_fingerprint($request->certificate);

        $builder = Certificate::query()->where("fingerprint", $fingerprint);
        if (!is_null($except))
            $builder->where("id", "!=", $except->id);
        if ($existsCertificate = $builder->first())
            throw ValidationException::withMessages(["指纹为[". $fingerprint ."]的证书已存在"]);

        return [
            "name" => $request->name,
            "fingerprint" => $fingerprint,
            "subject" => $certificateParsedResult["name"],
            "privateKey" => Certificate::encrypt($request->privateKey),
            "certificate" => $request->certificate,
            "valid_to" => date("Y-m-d H:i:s", $certificateParsedResult["validTo_time_t"]),
        ];
    }

    private function invalidCertificate()
    {
        throw ValidationException::withMessages(["Invalid certificate or private key"]);
    }
}
