<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * Allows to force the model to produce specific output format.
 * Only supported by OpenAI models, Nitro models, and some others - check the
 *  providers on the model page on https://openrouter.ai/docs#models to see if it's supported,
 *  and set `require_parameters` to true in your Provider Preferences. See
 *  https://openrouter.ai/docs#provider-routing
 */
class ResponseFormatData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string $type The format of the output, e.g. json, text, srt, verbose_json ...
     * @param mixed $json_schema The JSON schema for the output format.
     */
    public function __construct(
        public string $type = '',
        public mixed $json_schema = null,
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
                'type' => $this->type,
                'json_schema' => $this->json_schema,
            ],
            fn($value) => $value !== null,
        );
    }
}
