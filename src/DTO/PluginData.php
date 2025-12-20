<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * PluginData is the DTO for the plugins of the api call.
 *
 * Check https://openrouter.ai/docs/guides/features/web-search
 * Also check https://openrouter.ai/docs/guides/overview/multimodal/pdfs
 */
class PluginData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string $id Unique identifier for the plugin e.g. 'web' for web search, 'file-parser' for file inputs
     * @param string|null $engine Engine or method used by the plugin. e.g. "native", "exa", or undefined
     * @param int|null $max_results Maximum number of results to return. Defaults to 5 if not specified.
     * @param string|null $search_prompt Search prompt to guide the plugin's operation.
     * @param array<string, mixed>|null $pdf PDF file input plugin data e.g. ['engine' => 'pdf-text']
     */
    public function __construct(
        public string $id = '',
        public ?string $engine = null,
        public ?int $max_results = null,
        public ?string $search_prompt = null,
        public ?array $pdf = null,
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
                'engine' => $this->engine,
                'max_results' => $this->max_results,
                'search_prompt' => $this->search_prompt,
                'pdf' => $this->pdf,
            ],
            fn($value) => $value !== null,
        );
    }
}
