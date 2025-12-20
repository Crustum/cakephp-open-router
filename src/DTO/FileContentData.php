<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO for file/document content in messages.
 */
class FileContentData extends DataTransferObject
{
    /**
     * The allowed type value for file content.
     */
    public const string ALLOWED_TYPE = 'file';

    /**
     * Constructor.
     *
     * @param string $type Type of the content. (i.e. file)
     * @param \Crustum\OpenRouter\DTO\FileUrlData $file File data object containing URL or base64 data.
     */
    public function __construct(
        public string $type = self::ALLOWED_TYPE,
        public FileUrlData $file = new FileUrlData(''),
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
            'file' => $this->file->convertToArray(),
        ];
    }
}
