<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO for the contents.
 */
class TextContentData extends DataTransferObject
{
    /**
     * The allowed type for content.
     */
    public const string ALLOWED_TYPE = 'text';

    /**
     * Constructor.
     *
     * @param string $type Type of the content. (i.e. text)
     * @param string $text Text of the content.
     */
    public function __construct(
        public string $type = self::ALLOWED_TYPE,
        public string $text = '',
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
            'text' => $this->text,
        ];
    }
}
