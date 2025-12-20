<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * StreamingChoiceData is the DTO choice type for streaming responses
 */
class StreamingChoiceData extends ChoiceData
{
    /**
     * Constructor.
     *
     * @param \Crustum\OpenRouter\DTO\DeltaData $delta Any changed fields on a message during streaming.
     */
    public function __construct(
        public DeltaData $delta,
    ) {
        parent::__construct();
    }
}
