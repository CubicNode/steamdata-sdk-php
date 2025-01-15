<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata\Enums;

enum ServiceType: int
{
    case IMAGE_MODERATION = 0;
    case IP_INFO = 1;
}
