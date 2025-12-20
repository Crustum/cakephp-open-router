<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * CostResponseData is the response DTO for cost including token info and cost which consists of:
 *  - id
 *  - model
 *  - streamed
 *  - total_cost
 *  - origin
 *  - created_at
 *  - cancelled
 *  - finish_reason
 *  - generation_time
 *  - provider_name
 *  - tokens_prompt
 *  - tokens_completion
 *  - native_tokens_prompt
 *  - native_tokens_completion
 *  - num_media_prompt
 *  - num_media_completion
 *  - app_id
 *  - latency
 *  - moderation_latency
 *  - upstream_id
 *  - usage
 */
class CostResponseData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string $id ID of the cost request
     * @param string $model Name of the model e.g. mistralai/mistral-7b-instruct:free
     * @param float $total_cost Total cost of the request
     * @param string $origin Origin of the request
     * @param string $created_at Creation timestamp of the request
     * @param bool|null $streamed Whether the response was streamed
     * @param bool|null $cancelled Whether the request was cancelled
     * @param string|null $finish_reason Reason for finishing the request
     * @param int|null $generation_time Time taken for generation
     * @param string|null $provider_name Name of the provider
     * @param int|null $tokens_prompt Number of tokens in the prompt
     * @param int|null $tokens_completion Number of tokens in the completion
     * @param int|null $native_tokens_prompt Number of native tokens in the prompt
     * @param int|null $native_tokens_completion Number of native tokens in the completion
     * @param int|null $num_media_prompt Number of media items in the prompt
     * @param int|null $num_media_completion Number of media items in the completion
     * @param int|null $app_id Application ID associated with the request
     * @param int|null $latency Latency of the request in milliseconds
     * @param int|null $moderation_latency Moderation latency of the request in milliseconds
     * @param string|null $upstream_id Upstream ID associated with the request
     * @param float|null $usage Usage associated with the request
     */
    public function __construct(
        public string $id,
        public string $model,
        public float $total_cost,
        public string $origin,
        public string $created_at,
        public ?bool $streamed = null,
        public ?bool $cancelled = null,
        public ?string $finish_reason = null,
        public ?int $generation_time = null,
        public ?string $provider_name = null,
        public ?int $tokens_prompt = null,
        public ?int $tokens_completion = null,
        public ?int $native_tokens_prompt = null,
        public ?int $native_tokens_completion = null,
        public ?int $num_media_prompt = null,
        public ?int $num_media_completion = null,
        public ?int $app_id = null,
        public ?int $latency = null,
        public ?int $moderation_latency = null,
        public ?string $upstream_id = null,
        public ?float $usage = null,
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
                'id' => $this->id,
                'model' => $this->model,
                'streamed' => $this->streamed,
                'total_cost' => $this->total_cost,
                'origin' => $this->origin,
                'created_at' => $this->created_at,
                'cancelled' => $this->cancelled,
                'finish_reason' => $this->finish_reason,
                'generation_time' => $this->generation_time,
                'provider_name' => $this->provider_name,
                'tokens_prompt' => $this->tokens_prompt,
                'tokens_completion' => $this->tokens_completion,
                'native_tokens_prompt' => $this->native_tokens_prompt,
                'native_tokens_completion' => $this->native_tokens_completion,
                'num_media_prompt' => $this->num_media_prompt,
                'num_media_completion' => $this->num_media_completion,
                'app_id' => $this->app_id,
                'latency' => $this->latency,
                'moderation_latency' => $this->moderation_latency,
                'upstream_id' => $this->upstream_id,
                'usage' => $this->usage,
            ],
            fn($value) => $value !== null,
        );
    }
}
