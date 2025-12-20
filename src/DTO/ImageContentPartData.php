<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO for the image contents.
 */
class ImageContentPartData extends DataTransferObject
{
    /**
     * The allowed type for image content.
     */
    public const string ALLOWED_TYPE = 'image_url';

    /**
     * Constructor.
     *
     * @param string $type Type of the content. (i.e. image_url)
     * @param \Crustum\OpenRouter\DTO\ImageUrlData $image_url DTO of image url.
     */
    public function __construct(
        public string $type = self::ALLOWED_TYPE,
        public ImageUrlData $image_url = new ImageUrlData(''),
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
        return [
            'type' => $this->type,
            'image_url' => $this->image_url->convertToArray(),
        ];
    }
}
