<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * NonStreamingChoiceData is the DTO choice type for non-streaming responses.
 */
class NonStreamingChoiceData extends ChoiceData
{
    /**
     * Constructor.
     *
     * @param \Crustum\OpenRouter\DTO\MessageData $message DTO of the message data.
     */
    public function __construct(
        public MessageData $message,
    ) {
        parent::__construct();
    }
}
