<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * LimitResponseData is the response DTO for rate limit or credits left which consists of:
 *  - label
 *  - limit
 *  - usage
 *  - limit_remaining
 *  - is_free_tier
 *  - rate_limit (DTO object)
 */
class LimitResponseData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string|null $label Label of the limit e.g. sk-or-v1-f35...ebd
     * @param float|null $usage Number of credits used.
     * @param float|null $limit_remaining Remaining credits
     * @param int|null $limit Credit limit for the key, or null if unlimited.
     * @param bool|null $is_free_tier Whether the user has paid for credits before.
     * @param \Crustum\OpenRouter\DTO\RateLimitData|null $rate_limit Rate limit DTO data.
     */
    public function __construct(
        public ?string $label = null,
        public ?float $usage = null,
        public ?float $limit_remaining = null,
        public ?int $limit = null,
        public ?bool $is_free_tier = null,
        public ?RateLimitData $rate_limit = null,
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
                'label' => $this->label,
                'usage' => $this->usage,
                'limit_remaining' => $this->limit_remaining,
                'limit' => $this->limit,
                'is_free_tier' => $this->is_free_tier,
                'rate_limit' => $this->rate_limit?->toArray(),
            ],
            fn($value) => $value !== null,
        );
    }
}
