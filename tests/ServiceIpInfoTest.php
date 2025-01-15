<?php

declare(strict_types=1);

use Cubicnode\Cloud\Steamdata\Enums\HttpMethod;
use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\IpInfo;
use Cubicnode\Cloud\Steamdata\Enums\ServiceType;
use Cubicnode\Cloud\Steamdata\ServiceApiClient;
use Cubicnode\Cloud\Steamdata\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

use function Cubicnode\Cloud\Steamdata\normalize_url;

class ServiceIpInfoTest extends TestCase
{
    public function testGetIpGeolocation()
    {
        $serviceApiConfig = $this->createServiceApiConfig();

        $successResponseJson = '{"message":"success","code":00000,"data":{"ip":"127.0.0.1","network":null,"country":null,"province":null,"city":null,"latitude":null,"longitude":null}}';
        $errorResponseJson = '{"message":"error","code":00001,"errors":{}}';

        $serviceApiClient = new ServiceApiClient($serviceApiConfig, $this->createMockResponse([
            ['body' => $successResponseJson],
            [
                'body' => $errorResponseJson,
                'http_status_code' => 400,
                'http_request_method' => strtoupper(HttpMethod::GET->value),
                'http_request_uri' => normalize_url($serviceApiConfig->getGatewayUrl().IpInfo::GET_IP_GEOLOCATION->value.'?ip=127.0.0.1&language=zh_CN'),
            ],
        ]));
        $serviceApiClient->setServiceType(ServiceType::IP_INFO);
        $serviceApiClient->send(IpInfo::GET_IP_GEOLOCATION, ['ip' => '127.0.0.1', 'language' => 'zh_CN'])
            ->then(function (ResponseInterface $response) use ($successResponseJson) {
                $this->assertEquals(200, $response->getStatusCode());
                $this->assertEquals($successResponseJson, $response->getBody()->getContents());
            });
        $serviceApiClient->send(IpInfo::GET_IP_GEOLOCATION, ['ip' => '127.0.0.1', 'language' => 'zh_CN'])
            ->catch(function (RequestException $exception) use ($errorResponseJson) {
                $this->assertEquals(400, $exception->getResponse()->getStatusCode());
                $this->assertEquals($errorResponseJson, $exception->getResponse()->getBody()->getContents());
            })->wait();
    }

    public function testGetIpAsn()
    {
        $serviceApiConfig = $this->createServiceApiConfig();

        $successResponseJson = '{"message":"success","code":00000,"data":{"ip":"127.0.0.1","network":null,"autonomous_system_number":null,"autonomous_system_organization":null}}';
        $errorResponseJson = '{"message":"error","code":00001,"errors":{}}';

        $serviceApiClient = new ServiceApiClient($serviceApiConfig, $this->createMockResponse([
            ['body' => $successResponseJson],
            [
                'body' => $errorResponseJson,
                'http_status_code' => 400,
                'http_request_method' => strtoupper(HttpMethod::GET->value),
                'http_request_uri' => normalize_url($serviceApiConfig->getGatewayUrl().IpInfo::GET_IP_ASN->value.'?ip=127.0.0.1&language=zh_CN'),
            ],
        ]));
        $serviceApiClient->setServiceType(ServiceType::IP_INFO);
        $serviceApiClient->send(IpInfo::GET_IP_ASN, ['ip' => '127.0.0.1', 'language' => 'zh_CN'])
            ->then(function (ResponseInterface $response) use ($successResponseJson) {
                $this->assertEquals(200, $response->getStatusCode());
                $this->assertEquals($successResponseJson, $response->getBody()->getContents());
            });
        $serviceApiClient->send(IpInfo::GET_IP_ASN, ['ip' => '127.0.0.1', 'language' => 'zh_CN'])
            ->catch(function (RequestException $exception) use ($errorResponseJson) {
                $this->assertEquals(400, $exception->getResponse()->getStatusCode());
                $this->assertEquals($errorResponseJson, $exception->getResponse()->getBody()->getContents());
            })->wait();
    }
}
