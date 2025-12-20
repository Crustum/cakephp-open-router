<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * RateLimitData is the response DTO for rate limit which consists of:
 *  - requests
 *  - interval
 */
class RateLimitData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param int|null $requests Number of requests allowed.
     * @param string|null $interval In this interval, e.g. "10s"
     */
    public function __construct(
        public ?int $requests = null,
        public ?string $interval = null,
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
                'requests' => $this->requests,
                'interval' => $this->interval,
            ],
            fn($value) => $value !== null,
        );
    }
}
