<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\Client;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;

/**
 * OpenRouter HTTP Client
 *
 * Wraps Guzzle HTTP client with OpenRouter-specific configuration
 * and retry logic for handling rate limits and server errors.
 */
class OpenRouterClient
{
    protected ClientInterface $httpClient;

    protected string $apiKey;

    protected string $baseUrl;

    protected int $timeout;

    /**
     * Constructor.
     *
     * @param string|null $apiKey API key
     * @param string $baseUrl Base URL
     * @param int $timeout Request timeout in seconds
     * @param string $title Application title
     * @param string $referer Application referer URL
     */
    public function __construct(
        ?string $apiKey,
        string $baseUrl = 'https://openrouter.ai/api/v1/',
        int $timeout = 20,
        string $title = 'CakePHP OpenRouter',
        string $referer = '',
    ) {
        $this->apiKey = $apiKey ?? '';
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
        $this->timeout = $timeout;

        $retryOptions = [
            'max_retry_attempts' => 5,
            'retry_on_status' => [429, 500, 502, 503, 504],
            'retry_on_timeout' => true,
        ];

        $handlerStack = HandlerStack::create();
        $handlerStack->push(GuzzleRetryMiddleware::factory($retryOptions));

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ];

        if ($referer !== '') {
            $headers['HTTP-Referer'] = $referer;
        }

        if ($title !== '') {
            $headers['X-Title'] = $title;
        }

        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $this->timeout,
            'handler' => $handlerStack,
            'headers' => $headers,
        ]);
    }

    /**
     * Get the Guzzle client instance.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient(): ClientInterface
    {
        return $this->httpClient;
    }
}
