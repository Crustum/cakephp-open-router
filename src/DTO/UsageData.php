<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * UsageData is the DTO for the usage info of the api call.
 */
class UsageData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param int|null $prompt_tokens Equivalent to "native_tokens_completion" in the /generation API
     * @param int|null $completion_tokens Equivalent to "native_tokens_prompt"
     * @param int|null $total_tokens Sum of the above two fields ($prompt_tokens and $completion_tokens)
     * @param float|null $cost Credit usage of the request
     */
    public function __construct(
        public ?int $prompt_tokens = null,
        public ?int $completion_tokens = null,
        public ?int $total_tokens = null,
        public ?float $cost = null,
    ) {
        parent::__construct(...func_get_args());
    }

    /**
     * Convert to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter(
            [
                'prompt_tokens' => $this->prompt_tokens,
                'completion_tokens' => $this->completion_tokens,
                'total_tokens' => $this->total_tokens,
                'cost' => $this->cost,
            ],
            fn($value) => $value !== null,
        );
    }
}
