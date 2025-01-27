<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata;

use Carbon\Carbon;
use Cubicnode\Cloud\Steamdata\Enums\Algorithm;
use Cubicnode\Cloud\Steamdata\Enums\HttpMethod;
use Cubicnode\Cloud\Steamdata\Enums\ServiceName;
use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\ImageModeration as ImageModerationRoute;
use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\IpInfo as IpInfoRoute;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;

class SignatureGenerator
{
    private ?string $serviceName = null;

    private ?string $payload = null;

    public function __construct(
        private ServiceApiConfig $serviceApiConfig,
        private Request $request,
    ) {
        $this->serviceName = match ($this->request->getUri()->getPath()) {
            ImageModerationRoute::GET_IMAGE_SCORE->value => ServiceName::IMAGE_MODERATION->value,
            ImageModerationRoute::GET_IMAGE_SCORE_ASYNC->value => ServiceName::IMAGE_MODERATION->value,
            IpInfoRoute::GET_IP_GEOLOCATION->value => ServiceName::IP_INFO->value,
            IpInfoRoute::GET_IP_ASN->value => ServiceName::IP_INFO->value,
            default => throw new InvalidArgumentException('Invalid service route.'),
        };
    }

    public function canonicalHttpMethod(string $method): string
    {
        if (check_enum_value(HttpMethod::class, strtolower($method), false) !== true) {
            throw new InvalidArgumentException('Invalid HTTP method.');
        }

        return strtoupper($method);
    }

    public function canonicalUri(string $uri): string
    {
        if (! str_starts_with($uri, '/')) {
            $uri = '/'.$uri;
        }

        $parts = explode('/', $uri);

        $encodedParts = array_map(function ($item) {
            return rawurlencode($item);
        }, $parts);

        return implode('/', $encodedParts);
    }

    public function canonicalQueryString(string $queryString): string
    {
        if (empty($queryString)) {
            return '';
        }

        $pairs = explode('&', $queryString);

        $encodedPairs = [];
        foreach ($pairs as $pair) {
            $parts = explode('=', $pair, 2);
            $key = rawurlencode($parts[0]);
            $value = isset($parts[1]) ? rawurlencode($parts[1]) : '';
            $encodedPairs[$key] = $key.'='.$value;
        }

        ksort($encodedPairs);

        return implode('&', $encodedPairs);
    }

    public function canonicalHeaders(array $headers, string $signedHeaders): string
    {
        // necessary headers
        $requiredHeaders = [
            'host|:authority',
            'x-sd-api-version',
            'x-sd-datetime',
            'x-sd-instance-id',
        ];

        $optionalHeaders = [];
        $normalHeaders = [];
        foreach ($requiredHeaders as $header) {
            if (str_contains($header, '|')) {
                $parts = explode('|', $header);
                foreach ($parts as $part) {
                    $optionalHeaders[] = $part;
                }
            } else {
                $normalHeaders[] = $header;
            }
        }

        $lowercaseHeaders = array_change_key_case($headers, CASE_LOWER);

        $hasAnyOptionalHeader = false;
        foreach ($optionalHeaders as $header) {
            if (isset($lowercaseHeaders[strtolower($header)])) {
                $hasAnyOptionalHeader = true;
                break;
            }
        }

        $hasAllNormalHeaders = true;
        foreach ($normalHeaders as $header) {
            if (! isset($lowercaseHeaders[strtolower($header)])) {
                $hasAllNormalHeaders = false;
                break;
            }
        }

        if (! ($hasAnyOptionalHeader && $hasAllNormalHeaders)) {
            throw new InvalidArgumentException('Missing necessary headers.');
        }

        $signedHeadersArray = explode(';', $signedHeaders);
        $canonicalHeaders = [];

        foreach ($headers as $key => $value) {
            $lowerKey = strtolower($key);
            if (in_array($lowerKey, $signedHeadersArray)) {
                $canonicalHeaders[$lowerKey] = $lowerKey.':'.trim($value);
            }
        }

        ksort($canonicalHeaders);

        return implode("\n", $canonicalHeaders);
    }

    public function canonicalSignedHeaders(string $signedHeaders)
    {
        $headers = explode(';', $signedHeaders);
        sort($headers);

        return implode(';', $headers);
    }

    public function hashPayload(string $payload): string
    {
        return hash('sha256', $payload);
    }

    public function hashCanonicalRequest(
        string $httpMethod,
        string $uri,
        string $queryString,
        array $headers,
        string $signedHeaders,
        string $payload
    ): string {
        return hash('sha256', implode("\n", [
            $this->canonicalHttpMethod($httpMethod),
            $this->canonicalUri($uri),
            $this->canonicalQueryString($queryString),
            $this->canonicalHeaders($headers, $signedHeaders),
            $this->canonicalSignedHeaders($signedHeaders),
            $this->hashPayload($payload),
        ]));
    }

    public function createStringToSign(
        string $algorithm,
        string $requestDateTime,
        string $credentialScope,
        string $hashedCanonicalRequest
    ): string {
        return implode("\n", [
            $algorithm,
            $requestDateTime,
            $credentialScope,
            $hashedCanonicalRequest,
        ]);
    }

    public function createSigningKey(string $secretKey, string $date, string $region, string $service): string
    {
        $dateKey = hash_hmac('sha256', $date, strtoupper($this->serviceApiConfig->getSignatureVersion()).$secretKey, true);
        $regionKey = hash_hmac('sha256', $region, $dateKey, true);
        $serviceKey = hash_hmac('sha256', $service, $regionKey, true);

        return hash_hmac('sha256', $this->serviceApiConfig->getSignatureVersion().'_request', $serviceKey, true);
    }

    public function sign(string $signingKey, string $stringToSign): string
    {
        return hash_hmac('sha256', $stringToSign, $signingKey);
    }

    public function setPayload(string $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function createAuthorizationHeader(): string
    {
        $formatHeader = function (array $headers): array {
            $headerKV = [];
            foreach ($headers as $key => $value) {
                $headerKV[strtolower($key)] = $value[0];
            }

            return $headerKV;
        };

        $formattedHeaders = $formatHeader($this->request->getHeaders());

        $requestDateTime = $formattedHeaders['x-sd-datetime'];

        $createCredentialScope = fn () => implode('/', [
            Carbon::parse($requestDateTime)->format('Ymd'),
            $this->serviceApiConfig->getRegion(),
            $this->serviceName,
            $this->serviceApiConfig->getSignatureVersion().'_request',
        ]);

        $createSignedHeaders = function () use ($formattedHeaders) {
            $signedHeaders = [];
            foreach ($formattedHeaders as $key => $value) {
                $signedHeaders[] = $key;
            }
            sort($signedHeaders);

            return implode(';', $signedHeaders);
        };

        $stringToSign = $this->createStringToSign(
            strtoupper(Algorithm::SD1_HMAC_SHA256->value),
            $requestDateTime,
            $createCredentialScope(),
            $this->hashCanonicalRequest(
                $this->request->getMethod(),
                $this->request->getUri()->getPath(),
                $this->request->getUri()->getQuery(),
                $formattedHeaders,
                $createSignedHeaders(),
                $this->payload !== null ? $this->payload : $this->request->getBody()->getContents(),
            ),
        );

        $signingKey = $this->createSigningKey(
            $this->serviceApiConfig->getSecretAccessKey(),
            Carbon::parse($requestDateTime)->format('Ymd'),
            $this->serviceApiConfig->getRegion(),
            $this->serviceName,
        );

        return strtoupper(Algorithm::SD1_HMAC_SHA256->value).' '.implode(',', [
            'Credential='.$this->serviceApiConfig->getAccessKeyId().'/'.$createCredentialScope(),
            'SignedHeaders='.$createSignedHeaders(),
            'Signature='.$this->sign($signingKey, $stringToSign),
        ]);
    }
}
