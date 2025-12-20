<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO for image configuration in chat completion requests.
 */
class ImageConfigData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string|null $aspect_ratio The aspect ratio for generated images. Supported values: 1:1, 2:3, 3:2, 3:4, 4:3, 4:5, 5:4, 9:16, 16:9, 21:9. Default: 1:1 (1024×1024)
     */
    public function __construct(
        public ?string $aspect_ratio = null,
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
                'aspect_ratio' => $this->aspect_ratio,
            ],
            fn($value) => $value !== null,
        );
    }
}
