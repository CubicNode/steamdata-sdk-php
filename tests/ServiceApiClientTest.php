<?php

declare(strict_types=1);

use Cubicnode\Cloud\Steamdata\Tests\TestCase;
use Cubicnode\Cloud\Steamdata\ServiceApiConfig;
use Cubicnode\Cloud\Steamdata\Enums\ApiVersion;
use Cubicnode\Cloud\Steamdata\Enums\Region;
use Cubicnode\Cloud\Steamdata\Enums\SignatureVersion;
use Symfony\Component\Uid\Ulid;
use Cubicnode\Cloud\Steamdata\ServiceApiClient;
use Cubicnode\Cloud\Steamdata\Enums\ServiceType;
use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\ImageModeration;
use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\IpInfo;
use Cubicnode\Cloud\Steamdata\Exceptions\ServiceApiException;

class ServiceApiClientTest extends TestCase
{
    public function testSetServiceType()
    {
        $serviceApiClient = new ServiceApiClient($this->createServiceApiConfig());
        $serviceApiClient->setServiceType(ServiceType::IMAGE_MODERATION);
        $serviceApiClient->clearServiceType();
        $serviceApiClient->setServiceType(ServiceType::IMAGE_MODERATION->value);

        $this->assertTrue(true);
    }

    public function testSend()
    {
        $this->createTestImage();

        $serviceApiClient = new ServiceApiClient($this->createServiceApiConfig());
        $serviceApiClient->send(ImageModeration::GET_IMAGE_SCORE, ['image' => __DIR__.'/../test.png']);
        $serviceApiClient->send(ImageModeration::GET_IMAGE_SCORE_ASYNC, ['image' => __DIR__.'/../test.png', 'callback_url' => 'https://example.com/callback', 'marker_id' => '123456']);
        $serviceApiClient->send(ImageModeration::GET_IMAGE_SCORE->value, ['image' => __DIR__.'/../test.png']);
        $serviceApiClient->send(ImageModeration::GET_IMAGE_SCORE_ASYNC->value, ['image' => __DIR__.'/../test.png', 'callback_url' => 'https://example.com/callback', 'marker_id' => '123456']);
        $serviceApiClient->send(IpInfo::GET_IP_GEOLOCATION, ['ip' => '127.0.0.1', 'language' => 'zh_CN']);
        $serviceApiClient->send(IpInfo::GET_IP_ASN, ['ip' => '127.0.0.1', 'language' => 'zh_CN']);
        $serviceApiClient->send(IpInfo::GET_IP_GEOLOCATION->value, ['ip' => '127.0.0.1', 'language' => 'zh_CN']);
        $serviceApiClient->send(IpInfo::GET_IP_ASN->value, ['ip' => '127.0.0.1', 'language' => 'zh_CN']);

        $this->expectException(ServiceApiException::class);
        $serviceApiClient->send('invalid_route');
    }

    public function testThenMethod()
    {
        $this->expectException(ServiceApiException::class);
        
        $serviceApiClient = new ServiceApiClient($this->createServiceApiConfig());
        $serviceApiClient->then(function() {});
    }

    public function testCatchMethod()
    {
        $this->expectException(ServiceApiException::class);
        
        $serviceApiClient = new ServiceApiClient($this->createServiceApiConfig());
        $serviceApiClient->catch(function() {});
    }

    public function testWaitMethod()
    {
        $this->expectException(ServiceApiException::class);
        
        $serviceApiClient = new ServiceApiClient($this->createServiceApiConfig());
        $serviceApiClient->wait();
    }
}