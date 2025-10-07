<?php

namespace Vigilant\Uptime\Actions\Outpost;

class GenerateOutpostCertificate
{
    public function __construct(
        protected GenerateRootCertificate $rootCertificateGenerator,
    ) {}

    public function generate(string $commonName, string $outpostIp, int $validityDays = 30): array
    {
        $rootCert = $this->rootCertificateGenerator->getRootCertificate();
        $rootKey = $this->rootCertificateGenerator->getRootPrivateKey();

        $rootCertResource = openssl_x509_read($rootCert);
        $rootKeyResource = openssl_pkey_get_private($rootKey);

        // Generate new private key
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        $dn = [
            'countryName' => 'US',
            'stateOrProvinceName' => 'State',
            'localityName' => 'City',
            'organizationName' => 'Vigilant',
            'organizationalUnitName' => 'Outpost',
            'commonName' => $commonName,
        ];

        // Detect if IP or DNS and build SAN accordingly
        $sanEntry = filter_var($outpostIp, FILTER_VALIDATE_IP)
            ? "IP:{$outpostIp}"
            : "DNS:{$outpostIp}";

        // Build a minimal OpenSSL config that defines all required sections
        $tmpConfig = tempnam(sys_get_temp_dir(), 'openssl_');
        $configData = <<<CONF
[ req ]
default_bits       = 2048
distinguished_name = req_distinguished_name
req_extensions     = v3_req
prompt             = no

[ req_distinguished_name ]
CN = {$commonName}

[ v3_req ]
subjectAltName = {$sanEntry}
CONF;

        file_put_contents($tmpConfig, $configData);

        // Create CSR using the custom config (so SAN gets included)
        $csr = openssl_csr_new($dn, $privateKey, [
            'digest_alg' => 'sha256',
            'config' => $tmpConfig,
            'req_extensions' => 'v3_req',
        ]);

        if ($csr === false) {
            throw new \RuntimeException('Failed to generate CSR: '.openssl_error_string());
        }

        // Sign CSR with the root CA
        $cert = openssl_csr_sign(
            $csr,
            $rootCertResource,
            $rootKeyResource,
            $validityDays,
            [
                'digest_alg' => 'sha256',
                'config' => $tmpConfig,
                'x509_extensions' => 'v3_req',
            ]
        );

        if ($cert === false) {
            throw new \RuntimeException('Failed to sign certificate: '.openssl_error_string());
        }

        openssl_pkey_export($privateKey, $privateKeyPem);
        openssl_x509_export($cert, $certPem);

        @unlink($tmpConfig);

        return [
            'certificate' => $certPem,
            'private_key' => $privateKeyPem,
            'root_certificate' => $rootCert,
        ];
    }
}
