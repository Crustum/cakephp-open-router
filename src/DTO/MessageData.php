<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO that represents a message i.e. any changed fields on a message.
 */
class MessageData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param array<int, mixed>|string|null $content The content of the message.
     * @param string|null $role The entity that produced the message. Possible values are user, assistant, system, function, tool
     * @param string|null $refusal Refusal message
     * @param string|null $reasoning Reasoning for the message.
     * @param array<int, mixed>|null $tool_calls Calling tools e.g. function
     * @param string|null $tool_call_id That is the identifier that connects the tool result back to the tool call the LLM requested. Used to specify which tool to call when multiple tools are provided.
     * @param string|null $name An optional name for the participant. Provides the model information to differentiate between participants of the same role. e.g. name: "Moe"
     */
    public function __construct(
        public string|array|null $content = null,
        public ?string $role = null,
        public ?string $refusal = null,
        public ?string $reasoning = null,
        public ?array $tool_calls = null,
        public ?string $tool_call_id = null,
        public ?string $name = null,
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
                'content' => is_array($this->content)
                    ? array_map(function ($value) {
                        if (
                            $value instanceof TextContentData
                            || $value instanceof ImageContentPartData
                            || $value instanceof AudioContentData
                            || $value instanceof FileContentData
                        ) {
                            return $value->convertToArray();
                        } else {
                            return $value;
                        }
                    }, $this->content)
                    : $this->content,
                'role' => $this->role,
                'tool_calls' => !is_null($this->tool_calls)
                    ? array_map(function ($value) {
                        return $value->convertToArray();
                    }, $this->tool_calls)
                    : null,
                'tool_call_id' => $this->tool_call_id,
                'name' => $this->name,
            ],
            fn($value) => $value !== null,
        );
    }
}
