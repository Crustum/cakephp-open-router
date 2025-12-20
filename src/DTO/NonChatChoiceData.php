<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * NonChatChoiceData is the DTO choice type for non-chat responses
 */
class NonChatChoiceData extends ChoiceData
{
    /**
     * Constructor.
     *
     * @param string $text The text of the choice
     */
    public function __construct(
        public string $text,
    ) {
        parent::__construct();
    }
}
