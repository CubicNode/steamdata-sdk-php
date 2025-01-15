<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes;

enum IpInfo: string
{
    case GET_IP_GEOLOCATION = '/service-api/ip-info/get-ip-geolocation';
    case GET_IP_ASN = '/service-api/ip-info/get-ip-asn';
}
