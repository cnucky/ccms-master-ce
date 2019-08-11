<?php

namespace App\Http\Controllers\AdminAPI\Node\ComputeNode;

use App\Certificate;
use App\Constants\GlobalErrorCode;
use App\Constants\StatusCode;
use App\Node\ComputeNode;
use App\SystemConfiguration;
use App\TrustedCertificate;
use App\Utils\Certificate\Exception\PeerCertificateException;
use App\Utils\Certificate\PeerCertificate;
use App\Utils\Node\ComputeNode\ComputeNodeUtil;
use App\Utils\PKI;
use App\Utils\PKIBuilder;
use App\Utils\Time;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use YunInternet\CCMSCommon\Constants\Constants;
use YunInternet\CCMSCommon\Network\Exception\APIRequestException;
use YunInternet\Libvirt\Exception\CertificateNotTrustedException;
use YunInternet\Libvirt\Exception\ErrorCode;
use YunInternet\Libvirt\Exception\LibvirtException;

class ComputeNodeController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:index," . ComputeNode::class)->only([
            "edit",
            "index",
            "show",
            "statistics",
        ]);
        $this->middleware("can:create," . ComputeNode::class)->only([
            "store",
        ]);
        $this->middleware("can:update," . ComputeNode::class)->only([
            "refreshAllocatedCounter",
            "refreshComputeInstanceAndLocalVolume",
            "changeNOVNCBasicSetting",
            "update",
        ]);
        $this->middleware("can:delete," . ComputeNode::class)->only([
            "massDestroy",
            "destroy",
        ]);
    }

    /**
     * Controller for list&search compute nodes
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return ["result" => true, "computeNodes" => ComputeNode::all()];
    }

    public function show(Request $request, ComputeNode $computeNode)
    {
        $trustedCertificate = $computeNode->trustedCertificate;
        $trustedCertificateInformation = [];
        if ($trustedCertificate) {
            $trustedCertificateInformation = openssl_x509_parse($trustedCertificate->certificate);
            $trustedCertificateInformation["fingerprint"] = $trustedCertificate->fingerprint;
        }

        $clientCertificateInformation = openssl_x509_parse($computeNode->certificate);
        $clientCertificateInformation["fingerprint"] = openssl_x509_fingerprint($computeNode->certificate);

        return [
            "result" => true,
            "computeNode" => $computeNode->newQuery()->where("id", $computeNode->id)->with(["zone", "zone.region"])->first(),
            "trustedCertificateInformation" => $trustedCertificateInformation,
            "clientCertificateInformation" => $clientCertificateInformation,
            "nodeStatus" => [
                "loadAverage" => $computeNode->loadAverages()->orderByDesc("microtime")->first(),
                // "instanceCount" => $computeNode->instances()->count(),
                // "localVolumeCount" => $computeNode->localVolumes()->count(),
            ],
            "serverTime" => Time::getServerTime()
        ];
    }

    public function refreshAllocatedCounter(Request $request, ComputeNode $computeNode)
    {
        $computeNode->calculateAllocatedMemory();
        $computeNode->calculateAllocatedDiskCapacity();
        return $this->show($request, $computeNode);
    }

    public function refreshComputeInstanceAndLocalVolume(Request $request, ComputeNode $computeNode)
    {
        $computeNode->refreshComputeInstanceCounter();
        $computeNode->refreshLocalVolumeCounter();
        return $this->show($request, $computeNode);
    }

    public function statistics(Request $request, ComputeNode $computeNode)
    {
        $recentClosure = self::microtimeRecentClosure(3);
        return [
            "result" => true,
            "statistics" => [
                "cpuUsages" => $computeNode->cpuUsages()->where($recentClosure)->where("processor", "cpu")->get(),
                "loadAverages" => $computeNode->loadAverages()->where($recentClosure)->get(),
                "memoryUsages" => $computeNode->memoryUsages()->where($recentClosure)->get(),
                "diskIOUsages" => $computeNode->diskIOUsages()->where($recentClosure)->get(),
                "diskSpaceUsages" => $computeNode->diskSpaceUsages()->where($recentClosure)->get(),
                "bandwidthUsages" => $computeNode->bandwidthUsages()->where($recentClosure)->get(),
            ],
        ];
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->storeValidate($request);

        /**
         * @var TrustedCertificate $trustedCertificate
         * @var ComputeNodeUtil $computeNodeUtil
         */
        if (($certificateValidateResult = $this->clientCertificateValidate($request, $request->private_key, $request->certificate, $trustedCertificate, $computeNodeUtil)) !== true) {
            return $certificateValidateResult;
        }

        $nodeUUID = $computeNodeUtil->getNodeUUID();
        if (($validateResponse = $this->uuidExistsValidate($nodeUUID)) !== true) {
            return $validateResponse;
        }

        DB::beginTransaction();
        try {

            $computeNode = ComputeNode::query()->create([
                "zone_id" => $request->zone_id,
                "name" => $request->name,
                "description" => $request->description,
                "host" => $request->host,
                "trusted_certificate_id" => $trustedCertificate->id,
                "private_key" => $this->encrypt($request->private_key),
                "certificate" => $request->certificate,
                "uuid" => $nodeUUID,
                "reserved_memory_capacity_in_mib_unit" => $request->reserved_memory_capacity_in_mib_unit,
                "reserved_disk_capacity_in_gib_unit" => $request->reserved_disk_capacity_in_gib_unit,
                "max_instance" => $request->max_instance,
                "status" => $request->status === null ? StatusCode::STATUS_NORMAL : $request->status,
            ]);


            $token = $this->generateToken($request);

            $computeNodeUtil->registerMaster(SystemConfiguration::value("SYSTEM_ID"), $computeNode->id, SystemConfiguration::systemHost(), $token);

            $computeNode->token = bcrypt($token);
            $computeNode->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return ["result" => true, "item" => $computeNode];
    }

    /**
     * @param Request $request
     * @param ComputeNode $computeNode
     * @throws ValidationException
     * @throws APIRequestException
     */
    public function update(Request $request, ComputeNode $computeNode)
    {
        $this->storeValidate($request, false, $computeNode);

        $privateKey = $computeNode->privateKey();
        if (strlen($request->private_key)) {
            $privateKey = $request->private_key;
        }

        $certificate = $computeNode->certificate;
        if (strlen($request->certificate)) {
            $certificate = $request->certificate;
        }

        /**
         * @var TrustedCertificate $trustedCertificate
         * @var ComputeNodeUtil $computeNodeUtil
         */
        if (($certificateValidateResult = $this->clientCertificateValidate($request, $privateKey, $certificate, $trustedCertificate, $computeNodeUtil)) !== true) {
            return $certificateValidateResult;
        }

        $nodeUUID = $computeNodeUtil->getNodeUUID();
        if (($validateResponse = $this->uuidExistsValidate($nodeUUID, $computeNode)) !== true) {
            return $validateResponse;
        }

        $token = null;

        if (@$request->regenerateToken) {
            $token = $this->generateToken($request);
            $computeNode->token = bcrypt($token);
        }

        if ($computeNodeUtil->registerMaster(SystemConfiguration::value("SYSTEM_ID"), $computeNode->id, SystemConfiguration::systemHost(), $token)) {
            $computeNode->fill([
                "zone_id" => $request->zone_id,
                "name" => $request->name,
                "description" => $request->description,
                "host" => $request->host,
                "trusted_certificate_id" => $trustedCertificate->id,
                "private_key" => $this->encrypt($privateKey),
                "certificate" => $certificate,
                "uuid" => $nodeUUID,
                "reserved_memory_capacity_in_mib_unit" => $request->reserved_memory_capacity_in_mib_unit,
                "reserved_disk_capacity_in_gib_unit" => $request->reserved_disk_capacity_in_gib_unit,
                "max_instance" => $request->max_instance,
                "status" => $request->status === null ? StatusCode::STATUS_NORMAL : $request->status,
            ]);
        }

        $computeNode->save();

        return [
            "result" => true,
            "item" => $computeNode
        ];
    }

    public function changeNOVNCBasicSetting(Request $request, ComputeNode $computeNode)
    {
        $this->validate($request, [
            "no_vnc_host" => "nullable|max:255",
            "no_vnc_port" => "nullable|integer|min:1025|max:65535",
            "no_vnc_client_connect_port" => "nullable|integer|min:1|max:65535",
            "certificate_id" => "nullable|exists:certificates,id"
        ]);

        $computeNode->update([
            "no_vnc_host" => $request->no_vnc_host,
            "no_vnc_port" => $request->no_vnc_port,
            "no_vnc_client_connect_port" => $request->no_vnc_client_connect_port,
            "certificate_id" => $request->certificate_id,
        ]);

        $this->updateNoVNCSetting($computeNode);

        return $this->show($request, $computeNode);
    }

    public function destroy(ComputeNode $computeNode)
    {
        $computeNode->delete();
        return ["result" => true];
    }

    public function massDestroy(Request $request)
    {
        if ($request->has("items") && is_array($items = $request->items)) {
            $deletedItemCount = ComputeNode::query()->whereIn("id", $items)->delete();
        } else {
            $deletedItemCount = 0;
            $items = [];
        }

        return ["result" => true, "items" => $items, "deletedItemCount" => $deletedItemCount];
    }

    protected function encrypt($string)
    {
        return Crypt::encryptString($string);
    }

    private function updateNoVNCSetting(ComputeNode $computeNode)
    {
        $certificate = $computeNode->serverCertificate;
        if ($certificate) {
            $computeNode->createUtil()->updateNoVNCConfigurations($computeNode->no_vnc_port, $certificate->certificate, Certificate::decrypt($certificate->privateKey));
        }
    }

    private function uuidExistsValidate($uuid, ComputeNode $exceptComputeNode = null)
    {
        $query = ComputeNode::query()->where("uuid", $uuid);
        if (!is_null($exceptComputeNode))
            $query->where("id", "!=", $exceptComputeNode->id);

        if ($existsComputeNode = $query->first()) {
            return ["result" => false, "errno" => GlobalErrorCode::COMPUTE_NODE_ALREADY_EXISTS, "message" => "此节点已存在", "existsComputeNode" => $existsComputeNode];
        }
        return true;
    }

    /**
     * @param Request $request
     * @param bool $requireCertificate
     * @param ComputeNode|null $except
     * @throws ValidationException
     */
    private function storeValidate(Request $request, $requireCertificate = true, ComputeNode $except = null)
    {
        $nameUniqueValidateRule = Rule::unique("compute_nodes", "name");
        if (!is_null($except)) {
            $nameUniqueValidateRule->ignore($except->id);
        }
        $rules = [
            "zone_id" => "required|exists:zones,id",
            "name" => ["required", "max:32", $nameUniqueValidateRule],
            "description" => "nullable|max:255",
            "host" => "required|max:128",
            "reserved_memory_capacity_in_mib_unit" => "nullable|integer|min:1024|max:" . Constants::UINT32_MAX,
            "reserved_disk_capacity_in_gib_unit" => "nullable|integer|min:4|max:" . Constants::UINT32_MAX,
            "max_instance" => "nullable|integer|min:0|max:" . Constants::UINT32_MAX,
            "status" => [Rule::in([1, 2])],
        ];

        if ($requireCertificate) {
            $rules["private_key"] = "required";
            $rules["certificate"] = "required";
        }

        $this->validate($request, $rules, [], ["host" => "服务器地址", "private_key" => "客户端私钥", "certificate" => "客户端证书"]);
    }

    private function clientCertificateValidate(Request $request, $clientPrivateKey, $clientCertificate, &$trustedCertificate, &$computeNodeUtil)
    {
        $CACertificate = $this->getCACertificate($request->host);

        $trustedCertificate = $this->isCertificateInTrustedList($CACertificate, $CACertificateFingerPrint);

        // Is CA certificate can be trusted?
        if (is_null($trustedCertificate)) {
            if ($promoteResponse = $this->isNeePromoteForTrustResponse($request, $CACertificate, $CACertificateFingerPrint)) {
                return $promoteResponse;
            }

            /**
             * @var TrustedCertificate $trustedCertificate
             */
            $trustedCertificate = $this->trustCertificate($request, $CACertificate, $CACertificateFingerPrint);
        }

        $computeNodeUtil = $this->buildComputeNodeUtil($request, $clientPrivateKey, $clientCertificate, $trustedCertificate);

        try {
            $computeNodeUtil->nodeInfo();
        } catch (CertificateNotTrustedException $e) {
            throw ValidationException::withMessages(["客户端证书不被节点信任，请检查客户端证书信息。"]);
        } catch (LibvirtException $e) {
            switch ($e->getCode()) {
                case ErrorCode::UNABLE_IMPORT_CLIENT_CERTIFICATE:
                    $message = ["无法导入客户端证书，请检查证书格式是否正确。"];
                    break;
                case ErrorCode::UNABLE_SET_X509_KEY_AND_CERTIFICATE:
                    $message = ["无法使用秘钥，请检查秘钥格式是否正确。"];
                    break;
                case ErrorCode::FAILED_TO_VERIFY_PEER_CERTIFICATE:
                    $message = ["节点服务器所使用的证书不被信任，请检查节点所使用的服务器证书的subject alternative name中是否包含此处填写的“". htmlspecialchars($request->host) ."”，并尝试重新签发服务器证书。"];
                    break;
                case ErrorCode::CERTIFICATE_HAS_NOT_GOT_A_KNOWN_ISSUER:
                    $message = ["所使用的客户端证书并非由节点的CA证书签发，请尝试通过节点重新签发证书，并重启nginx与libvirtd。"];
                    break;
                case ErrorCode::UNABLE_READ_TLS_CONFIRMATION:
                    $message = ["节点未返回TLS确认信息，请检查证书是否已被吊销。如果证书签发后未重启libvirtd，请尝试重启libvirtd。"];
                    break;
                default:
                    $message = ["libvirt错误：" . $e->getMessage()];
            }

            throw ValidationException::withMessages($message);
        }

        return true;
    }

    private function getCACertificate($host)
    {
        $peerCertificate = new PeerCertificate("https://" . $host . ":2048");

        try {
            $CACertificate = $peerCertificate->getRootCertificate();
        } catch (PeerCertificateException $e) {
            ValidationException::withMessages(["无法获取服务器根证书：" . $e->getMessage()]);
        }

        return $CACertificate;
    }

    private function isCertificateInTrustedList($CACertificate, &$CACertificateFingerprint = null)
    {
        $CACertificateFingerprint = openssl_x509_fingerprint($CACertificate);

        return TrustedCertificate::query()->where("fingerprint", "=", $CACertificateFingerprint)->first();
    }

    private function isNeePromoteForTrustResponse(Request $request, $CACertificate, $CACertificateFingerprint)
    {
        // Promote for trust CA certificate
        if ($request->trust_fingerprint !== $CACertificateFingerprint) {
            $parsedX509 = openssl_x509_parse($CACertificate);
            return ["result" => false, "errno" => 20010, "fingerprint" => $CACertificateFingerprint, "parsedX509" => $parsedX509];
        }

        return false;
    }

    private function trustCertificate(Request $request, $CACertificate, $CACertificateFingerprint)
    {
        openssl_x509_export($CACertificate, $exportedX509);

        // Save certificate to trusted certificate table
        return TrustedCertificate::query()->create([
            "fingerprint" => $CACertificateFingerprint,
            "name" => "CA Certificate for " . $request->host,
            "certificate" => $exportedX509,
        ]);
    }

    private function buildComputeNodeUtil(Request $request, $clientPrivateKey, $clientCertificate, TrustedCertificate $trustedCertificate)
    {
        $PKI = new PKI($trustedCertificate->certificate, $clientPrivateKey, $clientCertificate);
        $PKIBuilder = new PKIBuilder(\App\Constants\PKI::TYPE_COMPUTE_NODE, $request->host, $PKI);
        $computeNodeUtil = new ComputeNodeUtil($request->host, $PKIBuilder);

        return $computeNodeUtil;
    }

    private function generateToken(Request $request)
    {
        return sha1(microtime() . mt_rand(100000000, 999999999) . $request->host . $request->certificate . $request->ip());
    }

    private static function microtimeRecentClosure($hours = 6)
    {
        $now = time();
        $seconds = $hours * 3600;
        $minSecond = $now - $seconds;

        return function ($builder) use ($now, $minSecond) {
            return $builder->where("microtime", ">=", $minSecond)->where("microtime", "<=", $now);
        };
    }
}
