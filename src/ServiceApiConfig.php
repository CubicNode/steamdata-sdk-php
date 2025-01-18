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

    public function setGatewayUrl(string $gatewayUrl): self
    {
        $this->gatewayUrl = $gatewayUrl;

        return $this;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function setApiVersion(string $apiVersion): self
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }

    public function setSignatureVersion(string $signatureVersion): self
    {
        $this->signatureVersion = $signatureVersion;

        return $this;
    }

    public function setAccessKeyId(string $accessKeyId): self
    {
        $this->accessKeyId = $accessKeyId;

        return $this;
    }

    public function setSecretAccessKey(string $secretAccessKey): self
    {
        $this->secretAccessKey = $secretAccessKey;

        return $this;
    }

    public function setInstanceId(string $instanceId): self
    {
        $this->instanceId = $instanceId;

        return $this;
    }
}
