<?php
declare(strict_types=1);

namespace Crustum\OpenRouter;

use Cake\Core\Configure;
use Crustum\OpenRouter\Client\OpenRouterClient;
use Crustum\OpenRouter\Request\OpenRouterRequest;

/**
 * OpenRouter Facade
 *
 * Provides static access to OpenRouter API functionality.
 */
class OpenRouter
{
    protected static ?OpenRouterClient $_client = null;

    protected static ?OpenRouterRequest $_request = null;

    /**
     * Get the OpenRouter client instance.
     *
     * @return \Crustum\OpenRouter\Client\OpenRouterClient
     */
    public static function getClient(): OpenRouterClient
    {
        if (static::$_client === null) {
            $apiKey = Configure::read('OpenRouter.api_key');
            $apiEndpoint = Configure::read('OpenRouter.api_endpoint', 'https://openrouter.ai/api/v1/');
            $apiTimeout = Configure::read('OpenRouter.api_timeout', 20);
            $title = Configure::read('OpenRouter.title', 'CakePHP OpenRouter');
            $referer = Configure::read('OpenRouter.referer', '');

            static::$_client = new OpenRouterClient(
                $apiKey,
                $apiEndpoint,
                $apiTimeout,
                $title,
                $referer,
            );
        }

        return static::$_client;
    }

    /**
     * Get the OpenRouter request handler.
     *
     * @return \Crustum\OpenRouter\Request\OpenRouterRequest
     */
    public static function getRequest(): OpenRouterRequest
    {
        if (static::$_request === null) {
            static::$_request = new OpenRouterRequest(static::getClient());
        }

        return static::$_request;
    }

    /**
     * Send a chat request.
     *
     * @param \Crustum\OpenRouter\DTO\ChatData $chatData Chat request data
     * @return \Crustum\OpenRouter\DTO\ErrorData|\Crustum\OpenRouter\DTO\ResponseData
     */
    public static function chat(DTO\ChatData $chatData): DTO\ErrorData|DTO\ResponseData
    {
        return static::getRequest()->chatRequest($chatData);
    }

    /**
     * Get cost information for a request.
     *
     * @param string $requestId Request ID
     * @return \Crustum\OpenRouter\DTO\CostResponseData
     */
    public static function cost(string $requestId): DTO\CostResponseData
    {
        return static::getRequest()->costRequest($requestId);
    }

    /**
     * Get rate limit information.
     *
     * @return \Crustum\OpenRouter\DTO\LimitResponseData
     */
    public static function limits(): DTO\LimitResponseData
    {
        return static::getRequest()->limitRequest();
    }

    /**
     * Reset static instances (useful for testing).
     *
     * @return void
     */
    public static function reset(): void
    {
        static::$_client = null;
        static::$_request = null;
    }
}
