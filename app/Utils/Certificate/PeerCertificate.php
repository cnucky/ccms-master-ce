<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-1
 * Time: 下午4:40
 */

namespace App\Utils\Certificate;


use App\Utils\Certificate\Exception\PeerCertificateException;

class PeerCertificate
{
    private $host;

    private $port;

    public function __construct($url)
    {
        $this->host = parse_url($url, PHP_URL_HOST);
        $this->port = parse_url($url, PHP_URL_PORT);
    }

    /**
     * @return string
     * @throws PeerCertificateException
     */
    public function getRootCertificate()
    {
        $streamContext = $this->createStreamContext();

        $stream = stream_socket_client("ssl://". $this->host .":" . $this->port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $streamContext);

        if ($stream === false)
            throw new PeerCertificateException($errstr, $errno);

        $params = stream_context_get_params($stream);

        fclose($stream);

        return array_pop($params['options']['ssl']['peer_certificate_chain']);
    }

    private function createStreamContext()
    {
        return stream_context_create([
            "ssl" => [
                "capture_peer_cert_chain" => true,
                "verify_peer" => false,
                "verify_peer_name" => false
            ]
        ]);
    }
}