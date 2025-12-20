<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO for the validation result.
 * Contains whether the validation is successful and an optional message for failure.
 */
class ValidationResultData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param bool $isValid Indicates if the validation passed.
     * @param string|null $message Message in case of validation failure.
     */
    public function __construct(
        public bool $isValid = false,
        public ?string $message = null,
    ) {
        parent::__construct(...func_get_args());
    }
}
