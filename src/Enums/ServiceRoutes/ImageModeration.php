<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata\Enums\ServiceRoutes;

enum ImageModeration: string
{
    case GET_IMAGE_SCORE = '/service-api/image-moderation/get-image-score';
    case GET_IMAGE_SCORE_ASYNC = '/service-api/image-moderation/get-image-score-async';
}
