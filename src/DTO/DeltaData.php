<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO that represents a message delta i.e. any changed fields on a message during streaming.
 */
class DeltaData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string|null $content The content of the message.
     * @param string|null $role The entity that produced the message. Possible values are user, assistant, system, function, tool
     * @param string|null $refusal Refusal message
     * @param string|null $reasoning Reasoning for the message
     * @param array<int, mixed>|null $toolCalls Calling tools e.g. function
     */
    public function __construct(
        public ?string $content = null,
        public ?string $role = null,
        public ?string $refusal = null,
        public ?string $reasoning = null,
        public ?array $toolCalls = null,
    ) {
        parent::__construct(...func_get_args());
    }
}
