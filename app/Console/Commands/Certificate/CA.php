<?php

namespace App\Console\Commands\Certificate;

use App\Constants\Certificates;
use Illuminate\Console\Command;

class CA extends Certificate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cert:make-ca ' . self::OPTIONS;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate CA certificate';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dn = $this->retrieveDistinguishedName(true);
        echo PHP_EOL;

        $privateKey = openssl_pkey_new(["private_key_bits" => 2048]);
        $csr = openssl_csr_new($dn, $privateKey, self::COMMON_OPENSSL_CONFIGURATIONS);
        $x509 = openssl_csr_sign($csr, null, $privateKey, 3650, self::COMMON_OPENSSL_CONFIGURATIONS);

        $this->exportPrivateKey($privateKey);
        $this->exportX509Certificate($x509);


        echo "Key and certificate generated.";
        echo PHP_EOL;
    }

    private static function hostname()
    {
        $hostname = trim(file_get_contents("/etc/hostname"));
        return self::fallbackOnEmpty($hostname, "localhost");
    }

    private static function fallbackOnEmpty($value, $fallback)
    {
        return empty($value) ? $fallback : $value;
    }
}
