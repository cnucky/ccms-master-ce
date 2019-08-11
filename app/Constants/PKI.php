<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-1
 * Time: 下午3:53
 */

namespace App\Constants;


interface PKI
{
    const PKI_ROOT_DIRECTORY = __DIR__ . "/../../resources/pki/";

    const VERSION_FILE_NAME = "version";

    const CA_CERTIFICATE_FILE_NAME = "cacert.pem";

    const CLIENT_PRIVATE_KEY_FILE_NAME = "clientkey.pem";

    const CLIENT_CERTIFICATE_FILE_NAME = "clientcert.pem";

    const PKI_TYPE_DIRECTORY = [
        0 => "compute_node"
    ];

    const TYPE_COMPUTE_NODE = 0;
}