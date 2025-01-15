<?php

declare(strict_types=1);

use Cubicnode\Cloud\Steamdata\Tests\TestCase;
use Cubicnode\Cloud\Steamdata\ServiceApiConfig;
use Cubicnode\Cloud\Steamdata\Enums\ApiVersion;
use Cubicnode\Cloud\Steamdata\Enums\Region;
use Cubicnode\Cloud\Steamdata\Enums\SignatureVersion;
use Symfony\Component\Uid\Ulid;

class ServiceApiConfigTest extends TestCase
{
    public function testConstructServiceApiConfig()
    {
        $serviceApiConfig = new ServiceApiConfig();

        $gatewayUrl = 'https://gateway.example.com';
        $region = Region::AP_EAST_1->value;
        $apiVersion = ApiVersion::V1_0->value;
        $signatureVersion = SignatureVersion::SD1->value;
        $accessKeyId = (string) new Ulid();
        $secretAccessKey = $this->faker->regexify('[A-Za-z0-9]{64}');
        $instanceId = $this->faker->uuid();

        $serviceApiConfig->setGatewayUrl($gatewayUrl);
        $serviceApiConfig->setRegion($region);
        $serviceApiConfig->setApiVersion($apiVersion);
        $serviceApiConfig->setSignatureVersion($signatureVersion);
        $serviceApiConfig->setAccessKeyId($accessKeyId);
        $serviceApiConfig->setSecretAccessKey($secretAccessKey);    
        $serviceApiConfig->setInstanceId($instanceId);

        $this->assertEquals($gatewayUrl, $serviceApiConfig->getGatewayUrl());
        $this->assertEquals($region, $serviceApiConfig->getRegion());
        $this->assertEquals($apiVersion, $serviceApiConfig->getApiVersion());
        $this->assertEquals($signatureVersion, $serviceApiConfig->getSignatureVersion());
        $this->assertEquals($accessKeyId, $serviceApiConfig->getAccessKeyId());
        $this->assertEquals($secretAccessKey, $serviceApiConfig->getSecretAccessKey());
        $this->assertEquals($instanceId, $serviceApiConfig->getInstanceId());
    }
}