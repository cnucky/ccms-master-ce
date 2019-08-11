<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-1
 * Time: 下午3:44
 */

namespace App\Utils;

use App\Constants\PKI as PKIConstants;
use App\Utils\Contract\PKIContract;

/**
 * Class PKI
 * @property-read $pkiDirectory
 * @property-read $CACertificateFilePath
 * @property-read $clientPrivateKeyFilePath
 * @property-read $clientCertificateFilePath
 * @package App\Utils
 */
class PKIBuilder
{
    private $pkiDirectory;

    private $versionFilePath;

    private $CACertificateFilePath;

    private $clientPrivateKeyFilePath;

    private $clientCertificateFilePath;

    private $PKIContract;

    private $type;

    private $id;

    public function __construct($typeCode, $id, PKIContract $PKIContract)
    {
        $this->PKIContract = $PKIContract;

        $this->type = PKIConstants::PKI_TYPE_DIRECTORY[$typeCode];

        $this->id = $id;

        $this->setPKIPath();
        $this->createPKIIfNotAvailable();
    }

    public function __get($name)
    {
        static $allows = [
            "pkiDirectory" => true,
            "CACertificateFilePath" => true,
            "clientPrivateKeyFilePath" => true,
            "clientCertificateFilePath" => true,
        ];

        if (array_key_exists($name, $allows))
            return $this->{$name};
        return null;
    }

    private function setPKIPath()
    {
        $this->pkiDirectory = PKIConstants::PKI_ROOT_DIRECTORY . "/" . $this->type . "/" . $this->id . "/";
        $this->versionFilePath = $this->pkiDirectory . PKIConstants::VERSION_FILE_NAME;
        $this->CACertificateFilePath = $this->pkiDirectory . PKIConstants::CA_CERTIFICATE_FILE_NAME;
        $this->clientPrivateKeyFilePath = $this->pkiDirectory . PKIConstants::CLIENT_PRIVATE_KEY_FILE_NAME;
        $this->clientCertificateFilePath = $this->pkiDirectory . PKIConstants::CLIENT_CERTIFICATE_FILE_NAME;
    }

    private function createPKIIfNotAvailable()
    {
        if (!is_dir($this->pkiDirectory))
            mkdir($this->pkiDirectory, 0700, true);

        if (!$this->isVersionValid($versionFP, $validVersion) || !$this->isCertificateFileExists()) {
            // Lock version file with exclusive lock
            flock($versionFP, LOCK_EX);

            // Back to start position
            fseek($versionFP, 0, SEEK_SET);

            ftruncate($versionFP, 0);

            fwrite($versionFP, $validVersion);
            fflush($versionFP);

            file_put_contents($this->CACertificateFilePath, $this->PKIContract->getCACertificate());
            file_put_contents($this->clientPrivateKeyFilePath, $this->PKIContract->getClientPrivateKey());
            file_put_contents($this->clientCertificateFilePath, $this->PKIContract->getClientCertificate());
        }

        flock($versionFP, LOCK_UN);
        fclose($versionFP);
    }

    private function isVersionValid(&$versionFP, &$validVersion)
    {
        $versionFP = fopen($this->versionFilePath, "c+");
        flock($versionFP, LOCK_SH);
        $versionContent = fgets($versionFP);
        return $versionContent === ($validVersion = $this->PKIContract->getVersion());
    }

    private function isCertificateFileExists()
    {
        return file_exists($this->CACertificateFilePath) && file_exists($this->clientPrivateKeyFilePath) && file_exists($this->clientCertificateFilePath);
    }
}