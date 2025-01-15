<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata;

use Carbon\Carbon;
use Exception;
use InvalidArgumentException;
use ValueError;

/**
 * @param class-string $enum
 */
function check_enum_value(string $enum, string $value, bool $throw = true): bool
{
    try {
        $enum::from($value);

        return true;
    } catch (ValueError $th) {
        if ($throw === true) {
            throw new InvalidArgumentException($th->getMessage());
        }

        return false;
    }

    return false;
}

function normalize_url(string $url): string
{
    $uniqid = uniqid();
    $_url = str_replace(['http://', 'https://'], ["http:{$uniqid}", "https:{$uniqid}"], $url);
    $_normalizedUrl = str_replace('#/+#', '/', $_url);
    $normalizedUrl = str_replace(["http:{$uniqid}", "https:{$uniqid}"], ['http://', 'https://'], $_normalizedUrl);

    try {
        if (filter_var($normalizedUrl, FILTER_VALIDATE_URL) !== false) {
            return $normalizedUrl;
        }
    } catch (Exception $th) {
        throw new Exception('Invalid URL.');
    }
}

function create_request_headers(ServiceApiConfig $serviceApiConfig, ?array $headers = null, bool $isMerge = true): array
{
    return $isMerge ? array_replace_recursive([
        'x-sd-api-version' => $serviceApiConfig->getApiVersion(),
        'x-sd-datetime' => Carbon::now('UTC')->format('Ymd\THis\Z'),
        'x-sd-instance-id' => $serviceApiConfig->getInstanceId(),
        'accept' => 'application/json',
        'accept-encoding' => 'gzip, deflate, br',
        'content-type' => 'application/json',
    ], $headers ?? []) : $headers;
}
