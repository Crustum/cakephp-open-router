<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

/**
 * Function tool that is called.
 */
class FunctionData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param string $name The name of the function e.g. getCurrentTemperature.
     * @param string|null $arguments Arguments for the function. JSON format arguments.
     * @param string|null $description A description of the function.
     * @param array<string, mixed>|null $parameters Parameters for the function. JSON Schema object.
     */
    public function __construct(
        public string $name = '',
        public ?string $arguments = null,
        public ?string $description = null,
        public ?array $parameters = null,
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
                'name' => $this->name,
                'arguments' => $this->arguments,
                'description' => $this->description,
                'parameters' => $this->parameters,
            ],
            fn($value) => $value !== null,
        );
    }
}
