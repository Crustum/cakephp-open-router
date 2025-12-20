<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * DTO for the audio contents.
 */
class AudioContentData extends DataTransferObject
{
    /**
     * The allowed type for audio content.
     */
    public const string ALLOWED_TYPE = 'input_audio';

    /**
     * Constructor.
     *
     * @param string $type Type of the content. (i.e. input_audio)
     * @param \Crustum\OpenRouter\DTO\InputAudioData $input_audio DTO of input audio.
     */
    public function __construct(
        public string $type = self::ALLOWED_TYPE,
        public InputAudioData $input_audio = new InputAudioData(''),
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
            'input_audio' => $this->input_audio->convertToArray(),
        ];
    }
}
