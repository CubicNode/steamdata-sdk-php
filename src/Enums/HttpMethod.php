<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata\Enums;

enum HttpMethod: string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case PATCH = 'patch';
    case DELETE = 'delete';
    case HEAD = 'head';
    case OPTIONS = 'options';
}
