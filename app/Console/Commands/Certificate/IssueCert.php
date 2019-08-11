<?php

namespace App\Console\Commands\Certificate;

use Illuminate\Console\Command;

class IssueCert extends Certificate
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cert:issue {--ca-key-file=} {--ca-cert-file=} {--save-full-chain-cert-to=} {--alt-name=*}' . self::OPTIONS;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Issue a certificate signed by CA';

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
        $this->line("");

        $CAKeyFile = "file://" . $this->option("ca-key-file");
        $CACertFile = "file://" . $this->option("ca-cert-file");

        $privateKey = openssl_pkey_new(["private_key_bits" => 2048]);

        $opensslConfigurationFilePath = $this->createOpenSSLSANConfigFile($dn, $configurationArray);

        $csr = openssl_csr_new($dn, $privateKey, $configurationArray);

        $x509 = openssl_csr_sign($csr, $CACertFile, $CAKeyFile, 3650, $configurationArray);

        $this->exportPrivateKey($privateKey);
        $exportedX509 = $this->exportX509Certificate($x509);

        $saveFullChain2 = $this->option("save-full-chain-cert-to");
        if (!empty($saveFullChain2)) {
            $this->renameOnFileExists($saveFullChain2);

            $this->info(sprintf("Export x509 full chain certificate to %s", $saveFullChain2));

            $fp = fopen($saveFullChain2, "w+");
            if ($fp) {
                try {
                    fwrite($fp, $exportedX509);
                    fwrite($fp, file_get_contents($CACertFile));
                    fflush($fp);
                } finally {
                    fclose($fp);
                }
            }
        }

        unlink($opensslConfigurationFilePath);

        $this->info("Certificate issued.");
    }
}
