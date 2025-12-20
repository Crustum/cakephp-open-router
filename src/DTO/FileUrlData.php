<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO for file URL/data wrapper.
 * Supports both direct URLs and base64 data URIs for documents.
 */
class FileUrlData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string $file_data File URL or base64 data URI. Formats: URL: 'https://example.com/document.pdf' or Base64: 'data:application/pdf;base64,JVBERi0xLjQK...'
     * @param string|null $filename Optional filename for context.
     */
    public function __construct(
        public string $file_data = '',
        public ?string $filename = null,
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
                'file_data' => $this->file_data,
                'filename' => $this->filename,
            ],
            fn($value) => $value !== null,
        );
    }
}
