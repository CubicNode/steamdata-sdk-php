<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata;

use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\ImageModeration;
use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\IpInfo;
use Cubicnode\Cloud\Steamdata\Enums\ServiceType;
use Cubicnode\Cloud\Steamdata\Exceptions\ServiceApiException;
use Cubicnode\Cloud\Steamdata\Services\ImageModeration as ImageModerationService;
use Cubicnode\Cloud\Steamdata\Services\IpInfo as IpInfoService;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;

class ServiceApiClient
{
    protected ?int $serviceType = null;

    protected ?PromiseInterface $promise = null;

    public function __construct(
        public ServiceApiConfig $config,
        public ?Client $client = null
    ) {
        $this->client = $client ?? new Client([
            'timeout' => 180,
        ]);
    }

    public function setServiceType(int|ServiceType $serviceType): self
    {
        if ($serviceType instanceof ServiceType) {
            $this->serviceType = $serviceType->value;
        } else {
            $this->serviceType = $serviceType;
        }

        return $this;
    }

    public function clearServiceType(): self
    {
        $this->serviceType = null;

        return $this;
    }

    public function send(
        ImageModeration|IpInfo|string $route,
        array $params = [],
    ): self {
        if ($route instanceof ImageModeration || $route instanceof IpInfo) {
            $route = $route->value;
        }

        $this->promise = match ($route) {
            ImageModeration::GET_IMAGE_SCORE->value => (new ImageModerationService($this->config, $this->client))->getImageScore($params['image']),
            ImageModeration::GET_IMAGE_SCORE_ASYNC->value => (new ImageModerationService($this->config, $this->client))->getImageScoreAsync($params['image'], $params['callback_url'], $params['marker_id']),
            IpInfo::GET_IP_GEOLOCATION->value => (new IpInfoService($this->config, $this->client))->getIpGeolocation($params['ip'], $params['language']),
            IpInfo::GET_IP_ASN->value => (new IpInfoService($this->config, $this->client))->getIpAsn($params['ip'], $params['language']),
            default => throw new ServiceApiException('Invalid service type'),
        };

        return $this;
    }

    public function then(callable $callback): self
    {
        if ($this->promise === null) {
            throw new ServiceApiException('No promise to resolve');
        }

        $this->promise = $this->promise->then($callback);

        return $this;
    }

    public function catch(callable $callback): self
    {
        if ($this->promise === null) {
            throw new ServiceApiException('No promise to reject');
        }

        $this->promise = $this->promise->then(null, $callback);

        return $this;
    }

    public function wait(): void
    {
        if ($this->promise === null) {
            throw new ServiceApiException('No promise to wait for');
        }

        $this->promise->wait();
    }
}
