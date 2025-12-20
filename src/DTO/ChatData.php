<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\DTO;

use Crustum\OpenRouter\Exceptions\OpenRouterValidationException;
use Crustum\OpenRouter\Rules\AllowedValues;
use Crustum\OpenRouter\Rules\XORFields;
use Crustum\OpenRouter\Types\RouteType;
use Crustum\OpenRouter\Types\ToolChoiceType;

/**
 * DTO for the chat completion request.
 */
class ChatData extends DataTransferObject
{
    /**
     * Constructor.
     *
     * @param array<int, \Crustum\OpenRouter\DTO\MessageData>|null $messages Message array consists of DTO data. xor-gated with prompt field
     * @param string|null $prompt Prompt string data. xor-gated with messages field
     * @param string|null $model Model name. If "model" is unspecified, uses the user's default. For more info: https://openrouter.ai/docs#models
     * @param \Crustum\OpenRouter\DTO\ResponseFormatData|null $response_format The format of the output, e.g. json, text, srt, verbose_json ...
     * @param bool $usage Include usage information in the response. This feature provides detailed information about token counts, costs, and caching status directly in your API responses (Default value is false, enabling usage accounting will add a few hundred milliseconds to the last response as the API calculates token counts and costs) See: https://openrouter.ai/docs/use-cases/usage-accounting
     * @param array<int, string>|string|null $stop Stop generation immediately if the model encounters any token specified in the stop array|string.
     * @param bool|null $stream Enable streaming.
     * @param int|null $max_tokens Range: [1, context_length) The maximum number of tokens that can be generated in the completion. Default 1024.
     * @param float|null $temperature Range: [0, 2] Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.
     * @param float|null $top_p Range: (0, 1] An alternative to sampling with temperature, called nucleus sampling, where the model considers the results of the tokens with top_p probability mass.
     * @param float|null $top_k Range: [1, Infinity) Not available for OpenAI models
     * @param float|null $frequency_penalty Range: [-2, 2] Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the model's likelihood to repeat the same line verbatim.
     * @param float|null $presence_penalty Range: [-2, 2] Positive values penalize new tokens based on whether they appear in the text so far, increasing the model's likelihood to talk about new topics.
     * @param float|null $repetition_penalty Range: (0, 2]
     * @param int|null $seed OpenAI only. This feature is in Beta. If specified, our system will make a best effort to sample deterministically, such that repeated requests with the same seed and parameters should return the same result.
     * @param array<int, mixed>|string|null $tool_choice Only natively supported by OpenAI models. For others, we submit a YAML-formatted string with these tools at the end of the prompt. none|auto or ToolCallData as {"type": "function", "function": {"name": "my_function"}}
     * @param array<int, \Crustum\OpenRouter\DTO\ToolCallData>|null $tools Tool calls (also known as function calling) allow you to give an LLM access to external tools.
     * @param array<string, mixed>|null $logit_bias Modify the likelihood of specified tokens appearing in the completion. e.g. {"50256": -100}
     * @param array<int, mixed>|null $transforms See "Prompt Transforms" section: https://openrouter.ai/docs#transforms
     * @param array<int, \Crustum\OpenRouter\DTO\PluginData>|null $plugins Plugins to use for the request e.g. for web search or pdf file input.
     * @param \Crustum\OpenRouter\DTO\WebSearchOptionsData|null $web_search_options Web search options for configuring native search behavior. Only applies when using native search (OpenAI, Anthropic, Perplexity, xAI models). For more info: https://openrouter.ai/docs/guides/features/web-search
     * @param array<int, string>|null $models The models array, which lets you automatically try other models if the primary model's providers are down, rate-limited, or refuse to reply due to content moderation required by all providers.
     * @param string|null $route Route type
     * @param \Crustum\OpenRouter\DTO\ProviderPreferencesData|null $provider See "Provider Routing" section: https://openrouter.ai/docs#provider-routing
     * @param bool|null $include_reasoning Enable think tokens. Note: This parameter is the legacy parameter and will be removed in the future. @deprecated Use '$reasoning' parameter instead (it is backward compatible with the old parameter).
     * @param \Crustum\OpenRouter\DTO\ReasoningData|null $reasoning For models that support it, the OpenRouter API can return Reasoning Tokens, also known as thinking tokens. See: https://openrouter.ai/docs/use-cases/reasoning-tokens
     * @param array<int, string>|null $modalities Modalities for the completion request. Specify both "image" and "text" to enable image generation. Example: ["image", "text"]
     * @param \Crustum\OpenRouter\DTO\ImageConfigData|null $image_config Configuration for image generation. See: https://openrouter.ai/docs/docs/overview/multimodal/image-generation
     */
    public function __construct(
        public ?array $messages = null,
        public ?string $prompt = null,
        public ?string $model = null,
        public ?ResponseFormatData $response_format = null,
        public bool $usage = false,
        public array|string|null $stop = null,
        public ?bool $stream = null,
        public ?int $max_tokens = 1024,
        public ?float $temperature = null,
        public ?float $top_p = null,
        public ?float $top_k = null,
        public ?float $frequency_penalty = null,
        public ?float $presence_penalty = null,
        public ?float $repetition_penalty = null,
        public ?int $seed = null,
        #[AllowedValues([ToolChoiceType::AUTO, ToolChoiceType::NONE])]
        public string|array|null $tool_choice = null,
        public ?array $tools = null,
        public ?array $logit_bias = null,
        public ?array $transforms = null,
        public ?array $plugins = null,
        public ?WebSearchOptionsData $web_search_options = null,
        public ?array $models = null,
        #[AllowedValues([RouteType::FALLBACK])]
        public ?string $route = null,
        public ?ProviderPreferencesData $provider = null,
        public ?bool $include_reasoning = false,
        public ?ReasoningData $reasoning = null,
        public ?array $modalities = null,
        public ?ImageConfigData $image_config = null,
    ) {
        $this->validateXorFields($this->messages, $this->prompt);
        $this->validateXorFields($this->model, $this->models);

        if ($this->reasoning === null && $this->include_reasoning !== null) {
            $this->reasoning = new ReasoningData(exclude: !$this->include_reasoning);
        }

        parent::__construct(...func_get_args());
    }

    /**
     * Validate the XOR fields and throw an exception if not valid.
     *
     * @param mixed $firstField
     * @param mixed $secondField
     * @return void
     * @throws \Crustum\OpenRouter\Exceptions\OpenRouterValidationException
     */
    private function validateXorFields(mixed $firstField, mixed $secondField): void
    {
        $xorFields = new XORFields($firstField, $secondField);
        $validationResult = $xorFields->validate();

        if (!$validationResult->isValid) {
            throw new OpenRouterValidationException($validationResult->message ?? 'Validation failed');
        }
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
                'messages' => !is_null($this->messages)
                    ? array_map(
                        /** @phpstan-ignore-next-line */
                        fn($value) => $value instanceof MessageData ? $value->convertToArray() : $value,
                        $this->messages,
                    )
                    : null,
                'prompt' => $this->prompt,
                'model' => $this->model,
                'response_format' => $this->response_format?->convertToArray(),
                'usage' => $this->usage ? ['include' => true] : null,
                'stop' => $this->stop,
                'stream' => $this->stream,
                'max_tokens' => $this->max_tokens,
                'temperature' => $this->temperature,
                'top_p' => $this->top_p,
                'top_k' => $this->top_k,
                'frequency_penalty' => $this->frequency_penalty,
                'presence_penalty' => $this->presence_penalty,
                'repetition_penalty' => $this->repetition_penalty,
                'seed' => $this->seed,
                'tool_choice' => $this->tool_choice,
                'tools' => !is_null($this->tools)
                    ? array_map(
                        /** @phpstan-ignore-next-line */
                        fn($value) => $value instanceof ToolCallData ? $value->convertToArray() : $value,
                        $this->tools,
                    )
                    : null,
                'logit_bias' => $this->logit_bias,
                'transforms' => $this->transforms,
                'plugins' => !is_null($this->plugins)
                    ? array_map(
                        /** @phpstan-ignore-next-line */
                        fn($value) => $value instanceof PluginData ? $value->convertToArray() : $value,
                        $this->plugins,
                    )
                    : null,
                'web_search_options' => $this->web_search_options?->convertToArray(),
                'models' => $this->models,
                'route' => $this->route,
                'provider' => $this->provider?->convertToArray(),
                'modalities' => $this->modalities,
                'image_config' => $this->image_config?->convertToArray(),
                'reasoning' => $this->reasoning?->convertToArray(),
            ],
            fn($value) => $value !== null,
        );
    }
}
