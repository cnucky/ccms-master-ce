<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-1
 * Time: 下午4:00
 */

namespace App\Utils;


use App\Utils\Contract\PKIContract;

class PKI implements PKIContract
{
    private $CACertificate;

    private $clientPrivateKey;

    private $clientCertificate;

    public function __construct($CACertificate, $clientPrivateKey, $clientCertificate)
    {
        $this->CACertificate = $CACertificate;

        $this->clientPrivateKey = $clientPrivateKey;

        $this->clientCertificate = $clientCertificate;
    }

    public function getVersion(): string
    {
        return md5($this->getCACertificate() . $this->getClientPrivateKey() . $this->getClientCertificate());
    }

    public function getCACertificate(): string
    {
        return $this->CACertificate;
    }

    public function getClientPrivateKey(): string
    {
        return $this->clientPrivateKey;
    }

    public function getClientCertificate(): string
    {
        return $this->clientCertificate;
    }
}