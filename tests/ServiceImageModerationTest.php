<?php

declare(strict_types=1);

use Cubicnode\Cloud\Steamdata\Enums\HttpMethod;
use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\ImageModeration;
use Cubicnode\Cloud\Steamdata\Enums\ServiceType;
use Cubicnode\Cloud\Steamdata\ServiceApiClient;
use Cubicnode\Cloud\Steamdata\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

use function Cubicnode\Cloud\Steamdata\normalize_url;

class ServiceImageModerationTest extends TestCase
{
    public function testGetImageScore()
    {
        $this->createTestImage();
        $serviceApiConfig = $this->createServiceApiConfig();

        $successResponseJson = '{"message":"success","code":00000,"data":{"safe_score":0.90,"unsafe_score":0.10}}';
        $errorResponseJson = '{"message":"error","code":00001,"errors":{}}';

        $serviceApiClient = new ServiceApiClient($serviceApiConfig, $this->createMockResponse([
            ['body' => $successResponseJson],
            [
                'body' => $errorResponseJson,
                'http_status_code' => 400,
                'http_request_method' => strtoupper(HttpMethod::POST->value),
                'http_request_uri' => normalize_url($serviceApiConfig->getGatewayUrl().ImageModeration::GET_IMAGE_SCORE->value),
            ],
        ]));
        $serviceApiClient->setServiceType(ServiceType::IMAGE_MODERATION);
        $serviceApiClient->send(ImageModeration::GET_IMAGE_SCORE, ['image' => __DIR__.'/../test.png'])
            ->then(function (ResponseInterface $response) use ($successResponseJson) {
                $this->assertEquals(200, $response->getStatusCode());
                $this->assertEquals($successResponseJson, $response->getBody()->getContents());
            });
        $serviceApiClient->send(ImageModeration::GET_IMAGE_SCORE, ['image' => __DIR__.'/../test.png'])
            ->catch(function (RequestException $exception) use ($errorResponseJson) {
                $this->assertEquals(400, $exception->getResponse()->getStatusCode());
                $this->assertEquals($errorResponseJson, $exception->getResponse()->getBody()->getContents());
            })->wait();
    }

    public function testGetImageScoreAsync()
    {
        $this->createTestImage();
        $serviceApiConfig = $this->createServiceApiConfig();

        $successResponseJson = '{"message":"success","code":00000,"data":{}}';
        $errorResponseJson = '{"message":"error","code":00001,"errors":{}}';

        $serviceApiClient = new ServiceApiClient($serviceApiConfig, $this->createMockResponse([
            ['body' => $successResponseJson],
            [
                'body' => $errorResponseJson,
                'http_status_code' => 400,
                'http_request_method' => strtoupper(HttpMethod::POST->value),
                'http_request_uri' => normalize_url($serviceApiConfig->getGatewayUrl().ImageModeration::GET_IMAGE_SCORE_ASYNC->value),
            ],
        ]));
        $serviceApiClient->setServiceType(ServiceType::IMAGE_MODERATION);
        $serviceApiClient->send(ImageModeration::GET_IMAGE_SCORE_ASYNC, ['image' => __DIR__.'/../test.png', 'callback_url' => 'https://example.com/callback', 'marker_id' => 'test'])
            ->then(function (ResponseInterface $response) use ($successResponseJson) {
                $this->assertEquals(200, $response->getStatusCode());
                $this->assertEquals($successResponseJson, $response->getBody()->getContents());
            })->wait();
        $serviceApiClient->send(ImageModeration::GET_IMAGE_SCORE_ASYNC, ['image' => __DIR__.'/../test.png', 'callback_url' => 'https://example.com/callback', 'marker_id' => 'test'])
            ->catch(function (RequestException $exception) use ($errorResponseJson) {
                $this->assertEquals(400, $exception->getResponse()->getStatusCode());
                $this->assertEquals($errorResponseJson, $exception->getResponse()->getBody()->getContents());
            })->wait();
    }
}
