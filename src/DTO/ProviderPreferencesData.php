<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO for the provider preferences.
 * for more info: https://openrouter.ai/docs#provider-routing
 */
class ProviderPreferencesData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param bool|null $allow_fallbacks Whether to allow backup providers to serve requests. true: (default) when the primary provider is unavailable, use the next best provider. false: use only the primary provider, and return the upstream error if it's unavailable.
     * @param bool|null $require_parameters Whether to filter providers to only those that support the parameters you've provided. If this setting is omitted or set to false, then providers will receive only the parameters they support, and ignore the rest.
     * @param string|null $data_collection Data collection setting. If no available model provider meets the requirement, your request will return an error. allow: (default) allow providers which store user data non-transiently and may train on it. deny: use only providers which do not collect user data.
     * @param array<string>|null $order An ordered list of provider names. The router will attempt to use the first provider in the subset of this list that supports your requested model, and fall back to the next if it is unavailable. If no providers are available, the request will fail with an error message.
     */
    public function __construct(
        public ?bool $allow_fallbacks = null,
        public ?bool $require_parameters = null,
        public ?string $data_collection = null,
        public ?array $order = null,
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
                'allow_fallbacks' => $this->allow_fallbacks,
                'require_parameters' => $this->require_parameters,
                'data_collection' => $this->data_collection,
                'order' => $this->order,
            ],
            fn($value) => $value !== null,
        );
    }
}
