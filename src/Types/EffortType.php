<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\Types;

/**
 * Reasoning effort level. Currently supported by the OpenAI o-series and Grok models.
 * See: https://openrouter.ai/docs/use-cases/reasoning-tokens
 */
final readonly class EffortType
{
    /**
     * Allocates a large portion of tokens for reasoning
     */
    public const string HIGH = 'high';

    /**
     * Allocates a moderate portion of tokens (approximately 50% of max_tokens)
     */
    public const string MEDIUM = 'medium';

    /**
     * Allocates a smaller portion of tokens (approximately 20% of max_tokens)
     */
    public const string LOW = 'low';

    /**
     * Allocates an even smaller portion of tokens (approximately 10% of max_tokens)
     */
    public const string MINIMAL = 'minimal';

    /**
     * Disables reasoning entirely
     */
    public const string NONE = 'none';
}
