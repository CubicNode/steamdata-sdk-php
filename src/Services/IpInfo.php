<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata\Services;

use Cubicnode\Cloud\Steamdata\Enums\HttpMethod;
use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\IpInfo as IpInfoRoute;
use Cubicnode\Cloud\Steamdata\ServiceApiConfig;
use Cubicnode\Cloud\Steamdata\SignatureGenerator;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;

use function Cubicnode\Cloud\Steamdata\create_request_headers;
use function Cubicnode\Cloud\Steamdata\normalize_url;

class IpInfo
{
    public function __construct(
        public ServiceApiConfig $serviceApiConfig,
        public Client $client,
    ) {
    }

    public function getIpGeolocation(string $ip, string $language): PromiseInterface
    {
        $request = new Request(
            method: strtoupper(HttpMethod::GET->value),
            uri: normalize_url($this->serviceApiConfig->getGatewayUrl().IpInfoRoute::GET_IP_GEOLOCATION->value)."?ip={$ip}&language={$language}",
            headers: create_request_headers($this->serviceApiConfig),
        );

        $signatureGenerator = new SignatureGenerator($this->serviceApiConfig, $request);
        $request = $request->withHeader('authorization', $signatureGenerator->createAuthorizationHeader());

        return $this->client->sendAsync($request);
    }

    public function getIpAsn(string $ip, string $language): PromiseInterface
    {
        $request = new Request(
            method: strtoupper(HttpMethod::GET->value),
            uri: normalize_url($this->serviceApiConfig->getGatewayUrl().IpInfoRoute::GET_IP_ASN->value)."?ip={$ip}&language={$language}",
            headers: create_request_headers($this->serviceApiConfig),
        );

        $signatureGenerator = new SignatureGenerator($this->serviceApiConfig, $request);
        $request = $request->withHeader('authorization', $signatureGenerator->createAuthorizationHeader());

        return $this->client->sendAsync($request);
    }
}
