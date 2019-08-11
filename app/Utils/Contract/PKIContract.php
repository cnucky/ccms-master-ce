<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-1
 * Time: 下午4:01
 */

namespace App\Utils\Contract;


interface PKIContract
{
    public function getVersion() : string;

    /**
     * @return string
     */
    public function getCACertificate() : string;

    /**
     * @return string
     */
    public function getClientPrivateKey() : string;

    /**
     * @return string
     */
    public function getClientCertificate() : string;
}