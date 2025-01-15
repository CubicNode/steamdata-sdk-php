<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata\Enums;

enum ServiceName: string
{
    case IMAGE_MODERATION = 'image-moderation';
    case IP_INFO = 'ip-info';
}
