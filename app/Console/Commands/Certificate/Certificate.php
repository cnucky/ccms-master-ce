<?php
/**
 * Created by PhpStorm.
 * Date: 19-1-28
 * Time: 上午1:57
 */

namespace App\Console\Commands\Certificate;


use Illuminate\Console\Command;

abstract class Certificate extends Command
{
    const OPTIONS = "{--save-key-to=} {--save-cert-to=} {--country-name=} {--state-or-province-name=} {--locality-name=} {--organization-name=} {--organizational-unit-name=} {--common-name=} {--email-address=}";

    const COMMON_OPENSSL_CONFIGURATIONS = [
        "digest_alg" => "sha256",
    ];

    protected $now;

    public function __construct()
    {
        parent::__construct();

        $this->now = time();
    }

    protected function createOpenSSLSANConfigFile(&$dn, &$configurationArray = null)
    {
        if (is_null($configurationArray))
            $configurationArray = self::COMMON_OPENSSL_CONFIGURATIONS;

        $opensslConfigurationFilePath = sprintf("%s/%s.%s.%s.conf", sys_get_temp_dir(), "openssl", mt_rand(1000000, 9999999), time());

        // Create openssl.conf file
        $oldUmask = umask(0077);
        $opensslConfigurationFilePointer = fopen($opensslConfigurationFilePath, "w+");
        umask($oldUmask);

        // Write basic configurations
        fwrite($opensslConfigurationFilePointer, <<<EOF
[req]
req_extensions = extension_section
x509_extensions	= extension_section
distinguished_name = dn
 
[dn]
 
[extension_section]
basicConstraints = CA:FALSE
keyUsage = nonRepudiation, digitalSignature, keyEncipherment
subjectAltName = @alt_names
 
[alt_names]

EOF
        );

        // Write SANs
        $i = 1;
        fwrite($opensslConfigurationFilePointer, sprintf("DNS.%d = %s", $i++, $dn["commonName"]));
        // fwrite($opensslConfigurationFilePointer, sprintf("\nDNS.%d = www.%s", $i++, $dn["commonName"]));
        foreach ($this->option("alt-name") as $name) {
            fwrite($opensslConfigurationFilePointer, sprintf("\nDNS.%d = %s", $i++, $name));
        }

        // Close configuration pointer
        fflush($opensslConfigurationFilePointer);
        fclose($opensslConfigurationFilePointer);

        $configurationArray["config"] = $opensslConfigurationFilePath;

        return $opensslConfigurationFilePath;
    }

    protected function exportPrivateKey($privateKey)
    {
        $oldUmask = umask(0077);

        $saveKey2 = $this->option("save-key-to");

        openssl_pkey_export($privateKey, $exportedPrivateKey);

        if (empty($saveKey2)) {
            $this->line($exportedPrivateKey);
        } else {
            $this->renameOnFileExists($saveKey2);
            $this->info(sprintf("Export private key to %s", $saveKey2));
            file_put_contents($saveKey2, $exportedPrivateKey);
        }

        umask($oldUmask);

        return $exportedPrivateKey;
    }

    protected function exportX509Certificate($x509)
    {
        $oldUmask = umask(0022);

        $saveCert2 = $this->option("save-cert-to");

        openssl_x509_export($x509, $exportedX509);

        if (empty($saveCert2)) {
            $this->line($exportedX509);
        } else {
            $this->renameOnFileExists($saveCert2);
            $this->info(sprintf("Export x509 certificate to %s", $saveCert2));
            file_put_contents($saveCert2, $exportedX509);
        }

        umask($oldUmask);

        return $exportedX509;
    }

    protected function retrieveDistinguishedName($print = false)
    {
        $commonName = self::fallbackOnEmpty($this->option("common-name"), self::hostname());

        $dn = [
            "commonName" => $commonName,
        ];

        $optionalValues = [
            "countryName" => $this->option("country-name"),
            "stateOrProvinceName" => $this->option("state-or-province-name"),
            "localityName" => $this->option("locality-name"),
            "organizationName" => $this->option("organization-name"),
            "organizationalUnitName" => $this->option("organizational-unit-name"),
            "emailAddress" => $this->option("email-address"),
        ];

        foreach ($optionalValues as $name => $value) {
            if (!empty($value))
                $dn[$name] = $value;
        }

        if ($print) {
            foreach ($dn as $name => $value)
                $this->info(sprintf("%s => %s", $name, $value));
        }

        return $dn;
    }

    protected function renameOnFileExists($file, $mode = 0600)
    {
        if (file_exists($file)) {
            $newFileName = sprintf("%s.%s", $file, $this->now);
            $this->info(sprintf("Rename %s to %s", $file, $newFileName));
            rename($file,  $newFileName);

            // Keep file secure
            chmod($newFileName, $mode);
        }
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