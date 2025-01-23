<?php

declare(strict_types=1);

use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\ImageModeration;
use Cubicnode\Cloud\Steamdata\SignatureGenerator;
use Cubicnode\Cloud\Steamdata\Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;

class SignatureGeneratorTest extends TestCase
{
    public function testCanonicalHttpMethod()
    {
        $serviceApiConfig = $this->createServiceApiConfig();

        $request = new Request('GET', 'https://example.com'.ImageModeration::GET_IMAGE_SCORE->value);
        $signatureGenerator = new SignatureGenerator($serviceApiConfig, $request);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP method.');

        $signatureGenerator->canonicalHttpMethod('invalid');
    }

    public function testCanonicalUri()
    {
        $serviceApiConfig = $this->createServiceApiConfig();

        $request = new Request('GET', 'https://example.com'.ImageModeration::GET_IMAGE_SCORE->value);
        $signatureGenerator = new SignatureGenerator($serviceApiConfig, $request);
        $signatureGenerator->canonicalUri('');

        $this->assertEquals('/', $signatureGenerator->canonicalUri(''));
    }

    public function testCanonicalHeaders()
    {
        $serviceApiConfig = $this->createServiceApiConfig();

        $instanceId = $this->faker->uuid();
        $request = new Request('GET', 'https://example.com'.ImageModeration::GET_IMAGE_SCORE->value, [
            'x-sd-instance-id' => $instanceId,
        ]);
        $signatureGenerator = new SignatureGenerator($serviceApiConfig, $request);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing necessary headers.');

        $signatureGenerator->canonicalHeaders([
            'x-sd-instance-id' => $instanceId,
        ], 'x-sd-instance-id');
    }
}
