<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO for the input audio which are data and format for the audio.
 */
class InputAudioData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string $data base64 encoded audio data
     * @param string|null $format Optional, detail about the audio format. Supported audio formats are: mp3, wav.
     */
    public function __construct(
        public string $data = '',
        public ?string $format = null,
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
                'data' => $this->data,
                'format' => $this->format,
            ],
            fn($value) => $value !== null,
        );
    }
}
