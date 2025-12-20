<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\Types;

/**
 * Audio can be provided in different formats for now: mp3, wav.
 * See: https://openrouter.ai/docs/features/multimodal/audio
 */
final readonly class AudioFormatType
{
    /**
     * MP3 audio format
     */
    public const string MP3 = 'mp3';

    /**
     * WAV audio format
     */
    public const string WAV = 'wav';
}
