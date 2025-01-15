<?php

declare(strict_types=1);

namespace Cubicnode\Cloud\Steamdata\Exceptions;

use Exception;

class ServiceApiException extends Exception
{
    public function __construct(
        string $message,
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
