<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO for the image url which are url and optional detail.
 */
class ImageUrlData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string $url URL or base64 encoded image data
     * @param string|null $detail Optional, defaults to 'auto'
     */
    public function __construct(
        public string $url = '',
        public ?string $detail = null,
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
                'url' => $this->url,
                'detail' => $this->detail,
            ],
            fn($value) => $value !== null,
        );
    }
}
