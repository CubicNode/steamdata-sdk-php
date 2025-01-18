<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata;

class ServiceApiConfig
{
    public function __construct(
        private ?string $gatewayUrl = null,
        private ?string $region = null,
        private ?string $apiVersion = null,
        private ?string $signatureVersion = null,
        private ?string $accessKeyId = null,
        private ?string $secretAccessKey = null,
        private ?string $instanceId = null,
    ) {
    }

    public function getGatewayUrl(): ?string
    {
        return $this->gatewayUrl;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function getApiVersion(): ?string
    {
        return $this->apiVersion;
    }

    public function getSignatureVersion(): ?string
    {
        return $this->signatureVersion;
    }

    public function getAccessKeyId(): ?string
    {
        return $this->accessKeyId;
    }

    public function getSecretAccessKey(): ?string
    {
        return $this->secretAccessKey;
    }

    public function getInstanceId(): ?string
    {
        return $this->instanceId;
    }

    public function setGatewayUrl(string $gatewayUrl): void
    {
        $this->gatewayUrl = $gatewayUrl;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function setApiVersion(string $apiVersion): void
    {
        $this->apiVersion = $apiVersion;
    }

    public function setSignatureVersion(string $signatureVersion): void
    {
        $this->signatureVersion = $signatureVersion;
    }

    public function setAccessKeyId(string $accessKeyId): void
    {
        $this->accessKeyId = $accessKeyId;
    }

    public function setSecretAccessKey(string $secretAccessKey): void
    {
        $this->secretAccessKey = $secretAccessKey;
    }

    public function setInstanceId(string $instanceId): void
    {
        $this->instanceId = $instanceId;
    }
}
