<?php
/**
 * Created by PhpStorm.
 * Date: 19-1-27
 * Time: 下午11:25
 */

namespace App\Constants;


interface Certificates
{
    const CA_KEY_FILE_PATH = "/etc/pki/libvirt/private/cakey.pem";

    const CA_CERTIFICATE_FILE_PATH = "/etc/pki/CA/cacert.pem";
}