# Steamdata SDK for PHP

这是适用于 PHP 的 Steamdata SDK，通过这个 SDK，您可以方便的在您的 PHP 项目中使用 Steamdata 的 API。

此包支持的 PHP 版本同步官方支持的 PHP 版本。

[https://www.php.net/supported-versions.php](https://www.php.net/supported-versions.php)


## 安装

仅能通过 `composer` 安装。
```bash
composer require cubicnode/steamdata-sdk-php
```


## 使用

```php
<?php

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use Cubicnode\Cloud\Steamdata\Enums\ApiVersion;
use Cubicnode\Cloud\Steamdata\Enums\Region;
use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\ImageModeration;
use Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes\IpInfo;
use Cubicnode\Cloud\Steamdata\Enums\ServiceType;
use Cubicnode\Cloud\Steamdata\Enums\SignatureVersion;
use Cubicnode\Cloud\Steamdata\ServiceApiClient;
use Cubicnode\Cloud\Steamdata\ServiceApiConfig;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

$serviceApiConfig = new ServiceApiConfig();
$serviceApiConfig->setGatewayUrl('https://api.steamdata.cloud.cubicnode.com');
$serviceApiConfig->setRegion(Region::AP_EAST_1->value); // 设定区域
$serviceApiConfig->setApiVersion(ApiVersion::V1_0->value); // 设定 API 版本
$serviceApiConfig->setSignatureVersion(SignatureVersion::SD1->value); // 设定签名版本
$serviceApiConfig->setAccessKeyId('<your-access-key-id>'); // 设置 AccessKeyId
$serviceApiConfig->setSecretAccessKey('<your-secret-access-key>'); // 设置 SecretAccessKey
$serviceApiConfig->setInstanceId('<your-instance-id>'); // 设置实例 ID

// 也可以这样设置
$serviceApiConfig = new ServiceApiConfig(
    gatewayUrl: 'https://api.steamdata.cloud.cubicnode.com',
    region: Region::AP_EAST_1->value,
    apiVersion: ApiVersion::V1_0->value,
    signatureVersion: SignatureVersion::SD1->value,
    accessKeyId: '<your-access-key-id>',
    secretAccessKey: '<your-secret-access-key>',
    instanceId: '<your-instance-id>',
)

// 创建客户端
$serviceApiClient = new ServiceApiClient($serviceApiConfig);
$serviceApiClient->setServiceType(ServiceType::IMAGE_MODERATION)
    ->send(ImageModeration::GET_IMAGE_SCORE_ASYNC, [
        'image' => __DIR__.'/test.png',
        'callback_url' => 'https://example.com/callback',
        'marker_id' => '0123456789',
    ])
    ->then(function (ResponseInterface $response) {
        echo $response->getBody()->getContents(); // 返回一个 json 字符串
    })
    ->catch(function (RequestException $exception) {
        echo $exception->getResponse()->getBody()->getContents(); // 一般情况下，返回一个 json 字符串
    })
    ->wait();
```

客户端需要先设置服务类型，然后调用 `send` 方法发送请求，`send` 方法接收两个参数，第一个参数是服务路由，第二个参数是请求参数，请求参数是一个关联数组，数组的键是参数名，数组的值是参数值。

请注意，请求是异步的，您需要调用 `wait` 方法等待请求完成。

客户端除了接受 `ServiceApiConfig` 配置之外，还支持设置自己的 `guzzle Client` 配置。
