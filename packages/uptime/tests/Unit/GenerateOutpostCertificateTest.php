<?php

namespace Vigilant\Uptime\Tests\Unit;

use Illuminate\Support\Facades\Storage;
use Vigilant\Uptime\Actions\Outpost\GenerateOutpostCertificate;
use Vigilant\Uptime\Actions\Outpost\GenerateRootCertificate;
use Vigilant\Uptime\Tests\TestCase;

class GenerateOutpostCertificateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up any existing certificates
        Storage::disk('local')->delete([
            'certificates/root-ca.key',
            'certificates/root-ca.crt',
        ]);
    }

    public function test_it_generates_root_ca_certificate(): void
    {
        $generator = new GenerateRootCertificate;

        $this->assertFalse($generator->exists());

        $generator->generate();

        $this->assertTrue($generator->exists());

        // Verify the certificate is valid
        $cert = $generator->getRootCertificate();
        $this->assertStringContainsString('BEGIN CERTIFICATE', $cert);

        // Verify the private key is valid
        $key = $generator->getRootPrivateKey();
        $this->assertStringContainsString('BEGIN PRIVATE KEY', $key);
    }

    public function test_it_generates_outpost_certificate(): void
    {
        $rootGenerator = new GenerateRootCertificate;
        $rootGenerator->generate();

        $outpostGenerator = new GenerateOutpostCertificate($rootGenerator);

        $certificate = $outpostGenerator->generate('test-outpost-192.168.1.1-8080', '192.168.1.1', 30);

        $this->assertArrayHasKey('certificate', $certificate);
        $this->assertArrayHasKey('private_key', $certificate);
        $this->assertArrayHasKey('root_certificate', $certificate);

        // Verify the certificate is valid
        $this->assertStringContainsString('BEGIN CERTIFICATE', $certificate['certificate']);
        $this->assertStringContainsString('BEGIN PRIVATE KEY', $certificate['private_key']);
        $this->assertStringContainsString('BEGIN CERTIFICATE', $certificate['root_certificate']);

        // Verify the certificate can be parsed
        $certResource = openssl_x509_read($certificate['certificate']);
        $this->assertNotFalse($certResource);

        $certData = openssl_x509_parse($certResource);
        $this->assertNotFalse($certData);
        $this->assertEquals('test-outpost-192.168.1.1-8080', $certData['subject']['CN']);
    }

    protected function tearDown(): void
    {
        // Clean up certificates after test
        Storage::disk('local')->delete([
            'certificates/root-ca.key',
            'certificates/root-ca.crt',
        ]);

        parent::tearDown();
    }
}
