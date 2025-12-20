<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

use Crustum\OpenRouter\Rules\AllowedValues;
use ReflectionClass;

/**
 * DataTransferObject is the base class for all DTOs.
 */
abstract class DataTransferObject
{
    /**
     * DataTransferObject constructor.
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->handlePropertyValidations();
    }

    /**
     * Handle property validations.
     *
     * @return void
     * @throws \ReflectionException
     */
    private function handlePropertyValidations(): void
    {
        $reflectionClass = new ReflectionClass($this);

        $nonNullProperties = array_filter(
            $reflectionClass->getProperties(),
            fn($property) => $property->getValue($this) !== null,
        );

        foreach ($nonNullProperties as $property) {
            foreach ($property->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();

                if ($instance instanceof AllowedValues) {
                    $instance->handle($property->getValue($this));
                }
            }
        }
    }
}
