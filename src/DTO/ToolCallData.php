<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * An array of tool calls the run step was involved in.
 * These can be associated with one of three types of tools: code_interpreter, file_search, or function.
 */
class ToolCallData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string|null $id ID of the tool call.
     * @param string|null $type Name of the tool. (i.e. function)
     * @param \Crustum\OpenRouter\DTO\FunctionData|null $function Function DTO object.
     */
    public function __construct(
        public ?string $id = null,
        public ?string $type = null,
        public ?FunctionData $function = null,
    ) {
        parent::__construct(...func_get_args());
    }

    /**
     * Convert to array.
     *
     * @return array<string, mixed>
     */
    public function convertToArray(): array
    {
        return array_filter(
            [
                'id' => $this->id,
                'type' => $this->type,
                'function' => $this->function?->convertToArray(),
            ],
            fn($value) => $value !== null,
        );
    }
}
