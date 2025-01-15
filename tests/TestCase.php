<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata\Tests;

use Cubicnode\Cloud\Steamdata\Enums\ApiVersion;
use Cubicnode\Cloud\Steamdata\Enums\Region;
use Cubicnode\Cloud\Steamdata\Enums\SignatureVersion;
use Cubicnode\Cloud\Steamdata\ServiceApiConfig;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use Symfony\Component\Uid\Ulid;

use function imagejpeg;

class TestCase extends PhpUnitTestCase
{
    public FakerGenerator $faker;

    public function __construct(string $name)
    {
        $this->faker = FakerFactory::create();
        parent::__construct($name);
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function createServiceApiConfig(): ServiceApiConfig
    {
        return new ServiceApiConfig(
            gatewayUrl: 'https://gateway.example.com',
            region: Region::AP_EAST_1->value,
            apiVersion: ApiVersion::V1_0->value,
            signatureVersion: SignatureVersion::SD1->value,
            accessKeyId: (string) new Ulid(),
            secretAccessKey: $this->faker->regexify('[A-Za-z0-9]{64}'),
            instanceId: $this->faker->uuid(),
        );
    }

    public function createMockResponse(array $responseData = []): Client
    {
        $mockHandler = [];
        foreach ($responseData as $key => $value) {
            if (! isset($value['http_status_code'])) {
                $value['http_status_code'] = 200;
            }

            if (! isset($value['headers'])) {
                $value['headers'] = [];
            }

            if (! isset($value['body'])) {
                $value['body'] = null;
            }

            if ($value['http_status_code'] >= 400) {
                $mockHandler[] = new RequestException(
                    $value['body'],
                    new Request($value['http_request_method'], $value['http_request_uri']),
                    new Response($value['http_status_code'], $value['headers'], $value['body'])
                );
            } else {
                $mockHandler[] = new Response($value['http_status_code'], $value['headers'], $value['body']);
            }
        }

        $mock = new MockHandler($mockHandler);

        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }

    public function createTestImage(): string
    {
        $imagePath = __DIR__.'/../test.png';

        $image = imagecreatetruecolor(100, 100);

        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);

        $textColor = imagecolorallocate($image, 0, 0, 0);
        $randomText = substr(md5(uniqid()), 0, 8);

        $fontSize = 5;
        imagestring($image, $fontSize, 20, 40, $randomText, $textColor);

        imagepng($image, $imagePath);
        imagedestroy($image);

        return $imagePath;
    }
}
