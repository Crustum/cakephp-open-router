<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * ReasoningData is the DTO for the reasoning parameters of the API call.
 * For more info: https://openrouter.ai/docs/use-cases/reasoning-tokens
 */
class ReasoningData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string|null $effort OpenAI-style reasoning effort setting
     * @param int|null $max_tokens Non-OpenAI-style reasoning effort setting. Note: Cannot be used simultaneously with effort.
     * @param bool|null $exclude Whether to exclude reasoning from the response
     * @param bool|null $enabled Enable reasoning with the default parameters. Default: inferred from `effort` or `max_tokens`
     */
    public function __construct(
        public ?string $effort = null,
        public ?int $max_tokens = null,
        public ?bool $exclude = false,
        public ?bool $enabled = null,
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
                'effort' => $this->effort,
                'max_tokens' => $this->max_tokens,
                'exclude' => $this->exclude,
                'enabled' => $this->enabled,
            ],
            fn($value) => $value !== null,
        );
    }
}
