<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\Rules;

use Attribute;
use Crustum\OpenRouter\Exceptions\OpenRouterValidationException;

/**
 * Validator class for checking whether the value is in allowed value list.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class AllowedValues
{
    /**
     * Constructor a new validation instance.
     *
     * @param array<int, mixed> $acceptableValues
     */
    public function __construct(protected array $acceptableValues = [])
    {
    }

    /**
     * Validates the allowed values.
     *
     * @param mixed $value
     * @return void
     * @throws \Crustum\OpenRouter\Exceptions\OpenRouterValidationException
     */
    public function handle(mixed $value): void
    {
        if (in_array($value, $this->acceptableValues, true) || $value === null) {
            return;
        }

        throw new OpenRouterValidationException(
            'Value is NOT allowed: ' . $value . ' - Allowed values: ' . implode(', ', $this->acceptableValues),
        );
    }
}
