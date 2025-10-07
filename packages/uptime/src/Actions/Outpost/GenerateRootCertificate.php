<?php

namespace Vigilant\Uptime\Actions\Outpost;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class GenerateRootCertificate
{
    private const ROOT_CA_KEY_PATH = 'certificates/root-ca.key';

    private const ROOT_CA_CERT_PATH = 'certificates/root-ca.crt';

    public function generate(): void
    {
        if ($this->exists()) {
            return;
        }

        $privateKey = openssl_pkey_new([
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        $dn = [
            'countryName' => 'NL',
            'stateOrProvinceName' => 'State',
            'localityName' => 'City',
            'organizationName' => 'Vigilant',
            'organizationalUnitName' => 'Uptime Monitoring',
            'commonName' => 'Vigilant Root CA',
        ];

        $csr = openssl_csr_new($dn, $privateKey, ['digest_alg' => 'sha256']);

        $cert = openssl_csr_sign($csr, null, $privateKey, 3650, ['digest_alg' => 'sha256']);

        openssl_pkey_export($privateKey, $privateKeyPem);
        openssl_x509_export($cert, $certPem);

        $this->disk()->put(self::ROOT_CA_KEY_PATH, $privateKeyPem);
        $this->disk()->put(self::ROOT_CA_CERT_PATH, $certPem);

        chmod($this->disk()->path(self::ROOT_CA_KEY_PATH), 0600);
        chmod($this->disk()->path(self::ROOT_CA_CERT_PATH), 0644);
    }

    public function exists(): bool
    {
        return $this->disk()->exists(self::ROOT_CA_KEY_PATH) && $this->disk()->exists(self::ROOT_CA_CERT_PATH);
    }

    public function getRootCertificatePath(): string
    {
        if (! $this->exists()) {
            $this->generate();
        }

        return $this->disk()->path(self::ROOT_CA_CERT_PATH);
    }

    public function getRootCertificate(): string
    {
        if (! $this->exists()) {
            $this->generate();
        }

        return $this->disk()->get(self::ROOT_CA_CERT_PATH);
    }

    public function getRootPrivateKey(): string
    {
        if (! $this->exists()) {
            $this->generate();
        }

        return $this->disk()->get(self::ROOT_CA_KEY_PATH);
    }

    protected function disk(): Filesystem
    {
        return Storage::disk('local');
    }
}
