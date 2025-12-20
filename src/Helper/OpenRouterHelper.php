<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\Helper;

use Crustum\OpenRouter\DTO\CostResponseData;
use Crustum\OpenRouter\DTO\LimitResponseData;
use Crustum\OpenRouter\DTO\RateLimitData;
use Crustum\OpenRouter\DTO\ResponseData;
use Crustum\OpenRouter\DTO\UsageData;
use JsonException;
use Psr\Http\Message\ResponseInterface;

/**
 * OpenRouter helper class is responsible for providing helper methods such as forming responses, decoding JSON, filtering stream responses and so on.
 */
final class OpenRouterHelper
{
    private static string $buffer = '';

    /**
     * Forms the response as ResponseData including id, model, object created, choices and usage if exits.
     *
     * @param mixed|null $response
     * @return \Crustum\OpenRouter\DTO\ResponseData
     */
    public function formChatResponse(mixed $response = null): ResponseData
    {
        $usageArray = $this->arrayGet($response, 'usage');
        $usage = new UsageData(
            prompt_tokens: $this->arrayGet($usageArray, 'prompt_tokens'),
            completion_tokens: $this->arrayGet($usageArray, 'completion_tokens'),
            total_tokens: $this->arrayGet($usageArray, 'total_tokens'),
            cost: $this->arrayGet($usageArray, 'cost'),
        );

        return new ResponseData(
            id: $this->arrayGet($response, 'id', ''),
            model: $this->arrayGet($response, 'model', ''),
            object: $this->arrayGet($response, 'object', ''),
            created: $this->arrayGet($response, 'created', 0),
            provider: $this->arrayGet($response, 'provider'),
            citations: $this->arrayGet($response, 'citations'),
            choices: $this->arrayGet($response, 'choices'),
            usage: $usage,
        );
    }

    /**
     * Forms the cost response as CostResponseData.
     * First decodes the json response, then map it in CostResponseData to return the response.
     *
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @return \Crustum\OpenRouter\DTO\CostResponseData
     */
    public function formCostsResponse(?ResponseInterface $response = null): CostResponseData
    {
        $response = $this->jsonDecode($response);

        return new CostResponseData(
            id: $this->arrayGet($response, 'data.id', ''),
            model: $this->arrayGet($response, 'data.model', ''),
            total_cost: $this->arrayGet($response, 'data.total_cost', 0.0),
            origin: $this->arrayGet($response, 'data.origin', ''),
            created_at: $this->arrayGet($response, 'data.created_at', ''),
            streamed: $this->arrayGet($response, 'data.streamed'),
            cancelled: $this->arrayGet($response, 'data.cancelled'),
            finish_reason: $this->arrayGet($response, 'data.finish_reason'),
            generation_time: $this->arrayGet($response, 'data.generation_time'),
            provider_name: $this->arrayGet($response, 'data.provider_name'),
            tokens_prompt: $this->arrayGet($response, 'data.tokens_prompt'),
            tokens_completion: $this->arrayGet($response, 'data.tokens_completion'),
            native_tokens_prompt: $this->arrayGet($response, 'data.native_tokens_prompt'),
            native_tokens_completion: $this->arrayGet($response, 'data.native_tokens_completion'),
            num_media_prompt: $this->arrayGet($response, 'data.num_media_prompt'),
            num_media_completion: $this->arrayGet($response, 'data.num_media_completion'),
            app_id: $this->arrayGet($response, 'data.app_id'),
            latency: $this->arrayGet($response, 'data.latency'),
            moderation_latency: $this->arrayGet($response, 'data.moderation_latency'),
            upstream_id: $this->arrayGet($response, 'data.upstream_id'),
            usage: $this->arrayGet($response, 'data.usage'),
        );
    }

    /**
     * Forms the response as LimitResponseData
     * First decodes the json response and get the result, then map it in LimitResponseData to return the response.
     *
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @return \Crustum\OpenRouter\DTO\LimitResponseData
     */
    public function formLimitResponse(?ResponseInterface $response = null): LimitResponseData
    {
        $response = $this->jsonDecode($response);

        $rateLimitArray = $this->arrayGet($response, 'data.rate_limit');
        $rateLimit = new RateLimitData(
            requests: $this->arrayGet($rateLimitArray, 'requests'),
            interval: $this->arrayGet($rateLimitArray, 'interval'),
        );

        return new LimitResponseData(
            label: $this->arrayGet($response, 'data.label'),
            usage: $this->arrayGet($response, 'data.usage'),
            limit_remaining: $this->arrayGet($response, 'data.limit_remaining'),
            limit: $this->arrayGet($response, 'data.limit'),
            is_free_tier: $this->arrayGet($response, 'data.is_free_tier'),
            rate_limit: $rateLimit,
        );
    }

    /**
     * Decodes response to json.
     *
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @return mixed|null
     */
    public function jsonDecode(?ResponseInterface $response = null): mixed
    {
        return $response ? json_decode((string)$response->getBody(), true) : null;
    }

    /**
     * It filters streaming response string so that response string is mapped into ResponseData.
     *
     * @param string $streamingResponse
     * @return array<int, \Crustum\OpenRouter\DTO\ResponseData>
     * @throws \JsonException
     */
    public function filterStreamingResponse(string $streamingResponse): array
    {
        $streamingResponse = self::$buffer . $streamingResponse;
        self::$buffer = '';

        $lines = explode("\n", $streamingResponse);

        $responseDataArray = [];

        $firstLineComplete = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            if (str_starts_with($line, 'data: ')) {
                $jsonData = substr($line, strlen('data: '));

                try {
                    $data = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);
                    $responseDataArray[] = $this->formChatResponse($data);
                    $firstLineComplete = true;
                } catch (JsonException $e) {
                    self::$buffer = $line;
                    continue;
                }
            } elseif ($trimmedLine === '' && !empty(self::$buffer)) {
                try {
                    $data = json_decode(self::$buffer, true, 512, JSON_THROW_ON_ERROR);
                    $responseDataArray[] = $this->formChatResponse($data);
                    self::$buffer = '';
                } catch (JsonException $e) {
                    continue;
                }
            // @phpstan-ignore-next-line
            } elseif (!str_starts_with($line, 'data: ') && $trimmedLine !== '') {
                $trimmedLine = trim($line);
                if ($trimmedLine !== '') {
                    if (!$firstLineComplete) {
                        self::$buffer = $line;
                        $firstLineComplete = true;
                    } else {
                        self::$buffer .= $line;
                    }
                } else {
                    self::$buffer .= $line;
                }
            } else {
                self::$buffer .= $line;
            }
        }

        return $responseDataArray;
    }

    /**
     * Get array value using dot notation (similar to Laravel Arr::get).
     *
     * @param mixed $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function arrayGet(mixed $array, string $key, mixed $default = null): mixed
    {
        if (!is_array($array)) {
            return $default;
        }

        if (str_contains($key, '.')) {
            $keys = explode('.', $key);
            $value = $array;
            foreach ($keys as $k) {
                if (!is_array($value) || !array_key_exists($k, $value)) {
                    return $default;
                }
                $value = $value[$k];
            }

            return $value;
        }

        return $array[$key] ?? $default;
    }
}
