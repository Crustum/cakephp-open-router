<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * ResponseData is the general response DTO which consists of:
 * - id
 * - model
 * - object
 * - created
 * - provider
 * - citations
 * - choices (DTO object)
 * - usage (DTO object)
 */
class ResponseData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string $id ID of the request which later can be used for cost request
     * @param string $model Name of the model e.g. mistralai/mistral-7b-instruct:free
     * @param string $object e.g. 'chat.completion' | 'chat.completion.chunk'
     * @param int $created Unix timestamp of created_at e.g. 1715621307
     * @param string|null $provider Model provider e.g. HuggingFace
     * @param array<string>|null $citations If using Perplexity Sonar, will return citations
     * @param array<int, mixed>|null $choices Depending on whether you set "stream" to "true" and whether you passed in "messages" or a "prompt", you get a different output shape.
     * @param \Crustum\OpenRouter\DTO\UsageData|null $usage Usage information of api request.
     */
    public function __construct(
        public string $id,
        public string $model,
        public string $object,
        public int $created,
        public ?string $provider = null,
        public ?array $citations = null,
        public ?array $choices = null,
        public ?UsageData $usage = null,
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
                'object' => $this->object,
                'created' => $this->created,
                'provider' => $this->provider,
                'citations' => $this->citations,
                'choices' => $this->choices,
                'usage' => $this->usage?->toArray(),
            ],
            fn($value) => $value !== null,
        );
    }
}
