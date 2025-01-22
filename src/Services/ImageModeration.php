<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata\Services;

use Cubicnode\Cloud\Steamdata\Enums\HttpMethod;
use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\ImageModeration as ImageModerationRoute;
use Cubicnode\Cloud\Steamdata\ServiceApiConfig;
use Cubicnode\Cloud\Steamdata\SignatureGenerator;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;

use function Cubicnode\Cloud\Steamdata\create_request_headers;
use function Cubicnode\Cloud\Steamdata\normalize_url;

class ImageModeration
{
    public function __construct(
        public ServiceApiConfig $serviceApiConfig,
        public Client $client,
    ) {
    }

    public function getImageScore(string $imagePath): PromiseInterface
    {
        $request = new Request(
            method: strtoupper(HttpMethod::POST->value),
            uri: normalize_url($this->serviceApiConfig->getGatewayUrl().ImageModerationRoute::GET_IMAGE_SCORE->value),
            headers: create_request_headers($this->serviceApiConfig),
            body: new MultipartStream([
                [
                    'name' => 'image',
                    'contents' => Utils::tryFopen($imagePath, 'r'),
                ],
            ]),
        );
        $request = $request->withHeader('content-type', 'multipart/form-data; boundary='.$request->getBody()->getBoundary());

        $signatureGenerator = new SignatureGenerator($this->serviceApiConfig, $request);
        $request = $request->withHeader('authorization', $signatureGenerator->createAuthorizationHeader());

        return $this->client->sendAsync($request);
    }

    public function getImageScoreAsync(string $imagePath, string $callbackUrl, string $markerId): PromiseInterface
    {
        $request = new Request(
            method: strtoupper(HttpMethod::POST->value),
            uri: normalize_url($this->serviceApiConfig->getGatewayUrl().ImageModerationRoute::GET_IMAGE_SCORE_ASYNC->value),
            headers: create_request_headers($this->serviceApiConfig),
            body: new MultipartStream([
                [
                    'name' => 'image',
                    'contents' => Utils::tryFopen($imagePath, 'r'),
                ],
                [
                    'name' => 'callback_url',
                    'contents' => $callbackUrl,
                ],
                [
                    'name' => 'marker_id',
                    'contents' => $markerId,
                ],
            ]),
        );
        $request = $request->withHeader('content-type', 'multipart/form-data; boundary='.$request->getBody()->getBoundary());

        $signatureGenerator = new SignatureGenerator($this->serviceApiConfig, $request);
        $request = $request->withHeader('authorization', $signatureGenerator->createAuthorizationHeader());

        return $this->client->sendAsync($request);
    }
}
