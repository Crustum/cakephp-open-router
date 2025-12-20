<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO for error messages.
 */
class ErrorData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param int $code Error code e.g. 400, 408 ...
     * @param string $message Error message.
     */
    public function __construct(
        public int $code,
        public string $message,
    ) {
        parent::__construct(...func_get_args());
    }
}
