<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO for web search options configuration.
 * For more info: https://openrouter.ai/docs/guides/features/web-search
 */
class WebSearchOptionsData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string|null $search_context_size Search context size determines how much search data is retrieved and processed. Options: 'low', 'medium', 'high'
     */
    public function __construct(
        public ?string $search_context_size = null,
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
                'search_context_size' => $this->search_context_size,
            ],
            fn($value) => $value !== null,
        );
    }
}
