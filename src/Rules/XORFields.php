<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\Rules;

use Crustum\OpenRouter\DTO\ValidationResultData;

/**
 * Validator class for XOR-gate first and second fields.
 * If firstField exists and secondField NOT exist, or vice versa, the output is TRUE -> validated.
 * If both firstField and secondField exist, or both are NOT exist, the output is FALSE -> validation failed.
 */
final readonly class XORFields
{
    /**
     * Constructor a new validation instance.
     *
     * @param mixed $firstField
     * @param mixed $secondField
     */
    public function __construct(protected mixed $firstField, protected mixed $secondField)
    {
    }

    /**
     * Validate XOR condition for two fields.
     *
     * @return \Crustum\OpenRouter\DTO\ValidationResultData
     */
    public function validate(): ValidationResultData
    {
        $isValid = (empty($this->firstField) xor empty($this->secondField));

        $xorFields = [
            ['messages', 'prompt'],
            ['model', 'models'],
        ];

        $result = [];
        foreach ($xorFields as $pair) {
            $result[] = implode(' and ', $pair);
        }

        $stringXorFields = implode(', ', $result);

        $message = $isValid
            ? null
            : "Fields {$stringXorFields} are XOR-gated, meaning exactly one field from each pair must be provided, but not both.";

        return new ValidationResultData(
            isValid: $isValid,
            message: $message,
        );
    }
}
