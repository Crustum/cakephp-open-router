<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * ChoiceData is the DTO for the choices of the api call.
 */
class ChoiceData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string|null $finish_reason Depends on the model. Ex: 'stop' | 'length' | 'content_filter' | 'tool_calls' | 'function_call' ...
     * @param \Crustum\OpenRouter\DTO\ErrorData|null $error Error returned from the API request
     */
    public function __construct(
        public ?string $finish_reason = null,
        public ?ErrorData $error = null,
    ) {
        parent::__construct(...func_get_args());
    }
}
