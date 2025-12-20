<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\Request;

use Crustum\OpenRouter\Client\OpenRouterClient;
use Crustum\OpenRouter\DTO\ChatData;
use Crustum\OpenRouter\DTO\CostResponseData;
use Crustum\OpenRouter\DTO\ErrorData;
use Crustum\OpenRouter\DTO\LimitResponseData;
use Crustum\OpenRouter\DTO\ResponseData;
use Crustum\OpenRouter\Helper\OpenRouterHelper;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * OpenRouter request and formed response class.
 *
 * OpenRouter doc: https://openrouter.ai/docs
 */
final class OpenRouterRequest
{
    protected OpenRouterClient $client;

    protected OpenRouterHelper $openRouterHelper;

    /**
     * Constructor.
     *
     * @param \Crustum\OpenRouter\Client\OpenRouterClient $client HTTP client
     */
    public function __construct(OpenRouterClient $client)
    {
        $this->client = $client;
        $this->openRouterHelper = new OpenRouterHelper();
    }

    /**
     * Sends a model request for the given chat conversation.
     *
     * @param \Crustum\OpenRouter\DTO\ChatData $chatData
     * @return \Crustum\OpenRouter\DTO\ErrorData|\Crustum\OpenRouter\DTO\ResponseData
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function chatRequest(ChatData $chatData): ErrorData|ResponseData
    {
        $chatCompletionPath = 'chat/completions';

        if ($chatData->stream) {
            return new ErrorData(
                code: 400,
                message: 'For stream chat completion please use "chatStreamRequest" method instead!',
            );
        }

        $chatData = $chatData->convertToArray();

        $options = [
            'json' => $chatData,
        ];

        $response = $this->client->getClient()->request(
            'POST',
            $chatCompletionPath,
            $options,
        );

        $response = $this->openRouterHelper->jsonDecode($response);

        return $this->openRouterHelper->formChatResponse($response);
    }

    /**
     * Sends a streaming request for the given chat conversation.
     *
     * @param \Crustum\OpenRouter\DTO\ChatData $chatData
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function chatStreamRequest(ChatData $chatData): PromiseInterface
    {
        $chatCompletionPath = 'chat/completions';

        $chatData->stream = true;

        $chatData = $chatData->convertToArray();

        $headers = [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
        ];

        $options = [
            'json' => $chatData,
            'headers' => $headers,
            'stream' => true,
        ];

        $promise = $this->client->getClient()->requestAsync(
            'POST',
            $chatCompletionPath,
            $options,
        );

        return $promise->then(
            function (ResponseInterface $response) {
                return $response->getBody();
            },
        );
    }

    /**
     * Sends a cost request for the given generation id.
     *
     * @param string $generationId
     * @return \Crustum\OpenRouter\DTO\CostResponseData
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function costRequest(string $generationId): CostResponseData
    {
        $costPath = 'generation?id=' . $generationId;

        $response = $this->client->getClient()->request(
            'GET',
            $costPath,
        );

        return $this->openRouterHelper->formCostsResponse($response);
    }

    /**
     * Sends limit request for the rate limit or credits left on an API key.
     *
     * @return \Crustum\OpenRouter\DTO\LimitResponseData
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function limitRequest(): LimitResponseData
    {
        $limitPath = 'auth/key';

        $response = $this->client->getClient()->request(
            'GET',
            $limitPath,
        );

        return $this->openRouterHelper->formLimitResponse($response);
    }

    /**
     * Filters streaming response string and maps it into an array of ResponseData.
     *
     * @param string $streamingResponse
     * @return array<int, \Crustum\OpenRouter\DTO\ResponseData>
     */
    public function filterStreamingResponse(string $streamingResponse): array
    {
        return $this->openRouterHelper->filterStreamingResponse($streamingResponse);
    }
}
