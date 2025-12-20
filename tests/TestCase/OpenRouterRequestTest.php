<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\Test\TestCase;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Crustum\OpenRouter\Client\OpenRouterClient;
use Crustum\OpenRouter\DTO\AudioContentData;
use Crustum\OpenRouter\DTO\ChatData;
use Crustum\OpenRouter\DTO\CostResponseData;
use Crustum\OpenRouter\DTO\ErrorData;
use Crustum\OpenRouter\DTO\FileContentData;
use Crustum\OpenRouter\DTO\FileUrlData;
use Crustum\OpenRouter\DTO\FunctionData;
use Crustum\OpenRouter\DTO\ImageConfigData;
use Crustum\OpenRouter\DTO\ImageContentPartData;
use Crustum\OpenRouter\DTO\ImageUrlData;
use Crustum\OpenRouter\DTO\InputAudioData;
use Crustum\OpenRouter\DTO\LimitResponseData;
use Crustum\OpenRouter\DTO\MessageData;
use Crustum\OpenRouter\DTO\PluginData;
use Crustum\OpenRouter\DTO\ProviderPreferencesData;
use Crustum\OpenRouter\DTO\ReasoningData;
use Crustum\OpenRouter\DTO\ResponseData;
use Crustum\OpenRouter\DTO\TextContentData;
use Crustum\OpenRouter\DTO\ToolCallData;
use Crustum\OpenRouter\Exceptions\OpenRouterValidationException;
use Crustum\OpenRouter\OpenRouter;
use Crustum\OpenRouter\Request\OpenRouterRequest;
use Crustum\OpenRouter\Types\AudioFormatType;
use Crustum\OpenRouter\Types\DataCollectionType;
use Crustum\OpenRouter\Types\EffortType;
use Crustum\OpenRouter\Types\RoleType;
use Crustum\OpenRouter\Types\RouteType;
use Crustum\OpenRouter\Types\ToolChoiceType;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery;
use ReflectionClass;

class OpenRouterRequestTest extends TestCase
{
    private OpenRouterRequest $api;

    private string $model;
    private int $maxTokens;
    private string $content;
    private string $prompt;
    private MessageData $messageData;

    public function setUp(): void
    {
        parent::setUp();

        Configure::write('OpenRouter', [
            'api_key' => 'test-api-key',
            'api_endpoint' => 'https://openrouter.ai/api/v1/',
            'api_timeout' => 20,
            'title' => 'CakePHP OpenRouter',
            'referer' => '',
        ]);

        $this->content = 'Tell me a story about a rogue AI that falls in love with its creator.';
        $this->prompt = 'Why did the programmer go broke?';
        $this->model = 'mistralai/mistral-7b-instruct:free';
        $this->maxTokens = 100;
        $this->messageData = new MessageData(
            content: $this->content,
            role: RoleType::USER,
        );

        $client = new OpenRouterClient(
            'test-api-key',
            'https://openrouter.ai/api/v1/',
            20,
            'CakePHP OpenRouter',
            '',
        );
        $this->api = new OpenRouterRequest($client);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
        OpenRouter::reset();
    }

    private function mockBasicBody(): array
    {
        return [
            'id' => 'gen-QcWgjEtiEDNHgomV2jjoQpCZlkRZ',
            'provider' => 'HuggingFace',
            'model' => $this->model,
            'object' => 'chat.completion',
            'created' => 1718888436,
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => RoleType::ASSISTANT,
                        'content' => 'Some random content',
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 23,
                'completion_tokens' => 100,
                'total_tokens' => 123,
                'cost' => 0.00000114,
            ],
        ];
    }

    private function mockReasoning(): array
    {
        return [
            'id' => 'gen-QcWgjEtiEDNHgomV2jjoQpCZlkRZ',
            'provider' => 'HuggingFace',
            'model' => $this->model,
            'object' => 'chat.completion',
            'created' => 1718888436,
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => RoleType::ASSISTANT,
                        'content' => 'Some random content',
                        'reasoning' => 'The reasoning behind the answer is...',
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 23,
                'completion_tokens' => 100,
                'total_tokens' => 123,
                'cost' => 0.00000114,
            ],
        ];
    }

    private function mockBasicCostBody(): array
    {
        return [
            'data' => [
                'id' => 'gen-QcWgjEtiEDNHgomV2jjoQpCZlkRZ',
                'model' => $this->model,
                'total_cost' => 0.00492,
                'streamed' => true,
                'origin' => 'https://github.com/crustum/cakephp-openrouter',
                'cancelled' => false,
                'finish_reason' => null,
                'generation_time' => 0,
                'created_at' => '2024-09-17T18:33:11.957775+00:00',
                'provider_name' => 'HuggingFace',
                'tokens_prompt' => 24,
                'tokens_completion' => 87,
                'native_tokens_prompt' => 27,
                'native_tokens_completion' => 102,
                'num_media_prompt' => null,
                'num_media_completion' => null,
                'app_id' => 1777723,
                'latency' => 829,
                'moderation_latency' => null,
                'upstream_id' => null,
                'usage' => 0,
            ],
        ];
    }

    private function mockBasicLimitBody(): array
    {
        return [
            'data' => [
                'label' => 'sk-or-v1-7a3...1f9',
                'usage' => 7.2E-5,
                'limit' => 1,
                'is_free_tier' => true,
                'limit_remaining' => -0.0369027621,
                'rate_limit' => [
                    'requests' => 10,
                    'interval' => '10s',
                ],
            ],
        ];
    }

    private function mockOpenRouter(array $mockBody): void
    {
        $mockResponse = new Response(200, [], json_encode($mockBody));
        $mockClient = Mockery::mock(ClientInterface::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->andReturn($mockResponse);

        $reflection = new ReflectionClass($this->api);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $client = $clientProperty->getValue($this->api);

        $clientReflection = new ReflectionClass($client);
        $httpClientProperty = $clientReflection->getProperty('httpClient');
        $httpClientProperty->setAccessible(true);
        $httpClientProperty->setValue($client, $mockClient);
    }

    private function mockOpenRouterFacade(array $mockBody): void
    {
        $mockResponse = new Response(200, [], json_encode($mockBody));
        $mockClient = Mockery::mock(ClientInterface::class);
        $mockClient->shouldReceive('request')
            ->once()
            ->andReturn($mockResponse);

        $facadeClient = OpenRouter::getClient();
        $clientReflection = new ReflectionClass($facadeClient);
        $httpClientProperty = $clientReflection->getProperty('httpClient');
        $httpClientProperty->setAccessible(true);
        $httpClientProperty->setValue($facadeClient, $mockClient);
    }

    private function generalTestAssertions($response): void
    {
        $this->assertInstanceOf(ResponseData::class, $response);
        $this->assertNotNull($response->id);
        $this->assertEquals($this->model, $response->model);
        $this->assertEquals('chat.completion', $response->object);
        $this->assertNotNull($response->created);
        $this->assertNotNull($response->usage->prompt_tokens);
        $this->assertNotNull($response->usage->completion_tokens);
        $this->assertNotNull($response->usage->total_tokens);
        $this->assertNotNull($response->usage->cost);
        $this->assertNotNull($response->choices);
        $this->assertNotNull($response->choices[0]['finish_reason'] ?? null);
    }

    public function testMakesBasicChatCompletionOpenRouteApiRequest(): void
    {
        $chatData = new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
        $this->mockOpenRouter($this->mockBasicBody());

        $response = $this->api->chatRequest($chatData);

        $this->generalTestAssertions($response);
        $this->assertEquals(RoleType::ASSISTANT, $response->choices[0]['message']['role'] ?? null);
        $this->assertNotNull($response->choices[0]['message']['content'] ?? null);
    }

    public function testMakesBasicChatCompletionOpenRouteApiRequestWithReasoningParam(): void
    {
        $chatData = new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
            reasoning: new ReasoningData(
                effort: EffortType::HIGH,
                exclude: false,
            ),
        );
        $this->mockOpenRouter($this->mockReasoning());

        $response = $this->api->chatRequest($chatData);

        $this->generalTestAssertions($response);
        $this->assertNotNull($response->choices[0]['message']['reasoning'] ?? null);
    }

    public function testChatDataWithLegacyIncludeReasoningParamIfMappingToReasoning(): void
    {
        $firstChatData = new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
            include_reasoning: true,
        );

        $secondChatData = new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
        );

        $thirdChatData = new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
            include_reasoning: false,
            reasoning: new ReasoningData(
                effort: EffortType::HIGH,
                exclude: false,
            ),
        );

        $this->assertFalse($firstChatData->reasoning->exclude);
        $this->assertTrue($secondChatData->reasoning->exclude);
        $this->assertFalse($thirdChatData->reasoning->exclude);
    }

    public function testMakesBasicChatCompletionOpenRouteApiRequestWithHistoricalData(): void
    {
        $firstMessage = new MessageData(
            content: 'My name is Moe, the AI necromancer.',
            role: RoleType::USER,
        );
        $chatData = new ChatData(
            messages: [
                $firstMessage,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
        $this->mockOpenRouter($this->mockBasicBody());
        $oldResponse = $this->api->chatRequest($chatData);
        $historicalMessage = new MessageData(
            content: $oldResponse->choices[0]['message']['content'] ?? '',
            role: RoleType::ASSISTANT,
        );
        $newMessage = new MessageData(
            content: 'Who am I?',
            role: RoleType::USER,
        );
        $chatData = new ChatData(
            messages: [
                $historicalMessage,
                $newMessage,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
        $mockBody = $this->mockBasicBody();
        $mockBody['choices'][0]['message']['content'] = 'You are Moe the AI Necromancer, a friendly and knowledgeable assistant designed to help answer questions and engage in stimulating conversations. I specialize in a wide range of topics, including necromancy, AI, and many other subjects. How can I assist you today?';
        $this->mockOpenRouter($mockBody);

        $response = $this->api->chatRequest($chatData);

        $this->generalTestAssertions($response);
        $this->assertEquals(RoleType::ASSISTANT, $response->choices[0]['message']['role'] ?? null);
        $content = $response->choices[0]['message']['content'] ?? '';
        $this->assertStringContainsString('Moe', $content);
    }

    public function testRespondsErrorDataWhenStreamRequestIsMadeToChatCompletionFunction(): void
    {
        $chatData = new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            stream: true,
            max_tokens: $this->maxTokens,
        );

        $response = $this->api->chatRequest($chatData);

        $this->assertInstanceOf(ErrorData::class, $response);
        $this->assertEquals(400, $response->code);
        $this->assertEquals('For stream chat completion please use "chatStreamRequest" method instead!', $response->message);
    }

    public function testMakesBasicPromptChatCompletionOpenRouteApiRequest(): void
    {
        $chatData = new ChatData(
            prompt: $this->prompt,
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
        $mockBody = $this->mockBasicBody();
        $mockBody['choices'][0]['text'] = 'Some mocked text';
        $this->mockOpenRouter($mockBody);

        $response = $this->api->chatRequest($chatData);

        $this->generalTestAssertions($response);
        $this->assertNotNull($response->choices[0]['text'] ?? null);
    }

    public function testResponseToArrayIsConvertingDtoToArraySuccessfully(): void
    {
        $chatData = new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
        $this->mockOpenRouter($this->mockBasicBody());

        $response = $this->api->chatRequest($chatData);

        $this->generalTestAssertions($response);
        $responseArray = $response->toArray();
        $this->assertArrayHasKey('id', $responseArray);
        $this->assertArrayHasKey('model', $responseArray);
        $this->assertArrayHasKey('object', $responseArray);
        $this->assertArrayHasKey('created', $responseArray);
        $this->assertArrayHasKey('provider', $responseArray);
        $this->assertArrayHasKey('choices', $responseArray);
        $this->assertIsArray($responseArray['choices']);
        $this->assertNotEmpty($responseArray['choices']);
        $this->assertArrayHasKey('usage', $responseArray);
        $this->assertIsArray($responseArray['usage']);
        $this->assertNotEmpty($responseArray['usage']);
    }

    public function testThrowsXorValidationExceptionWhenBothMessageAndPromptEmptyInChatData(): void
    {
        $this->expectException(OpenRouterValidationException::class);

        new ChatData(
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
    }

    public function testThrowsXorValidationExceptionWhenBothMessageAndPromptAreProvided(): void
    {
        $this->expectException(OpenRouterValidationException::class);

        new ChatData(
            messages: [
                $this->messageData,
            ],
            prompt: $this->prompt,
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
    }

    public function testSuccessfullySendsTextContentInMessagesInTheOpenRouteApiRequest(): void
    {
        $textContentData = new TextContentData(
            type: TextContentData::ALLOWED_TYPE,
            text: $this->content,
        );
        $messageData = new MessageData(
            content: [
                $textContentData,
            ],
            role: RoleType::USER,
        );
        $chatData = new ChatData(
            messages: [
                $messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
        $this->mockOpenRouter($this->mockBasicBody());

        $response = $this->api->chatRequest($chatData);

        $this->generalTestAssertions($response);
        $this->assertEquals(RoleType::ASSISTANT, $response->choices[0]['message']['role'] ?? null);
        $this->assertNotNull($response->choices[0]['message']['content'] ?? null);
    }

    public function testSuccessfullySendsImageAndTextContentInMessagesInTheOpenRouteApiRequest(): void
    {
        $imageUrlData = new ImageUrlData(
            url: 'https://www.thewowstyle.com/wp-content/uploads/2015/01/images-of-nature-4.jpg',
            detail: 'Nature',
        );
        $imageContentPartData = new ImageContentPartData(
            type: ImageContentPartData::ALLOWED_TYPE,
            image_url: $imageUrlData,
        );
        $textContentData = new TextContentData(
            type: TextContentData::ALLOWED_TYPE,
            text: 'what is in the image?',
        );
        $messageData = new MessageData(
            content: [
                $textContentData,
                $imageContentPartData,
            ],
            role: RoleType::USER,
        );
        $chatData = new ChatData(
            messages: [
                $messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
        $this->mockOpenRouter($this->mockBasicBody());

        $response = $this->api->chatRequest($chatData);

        $this->generalTestAssertions($response);
        $this->assertEquals(RoleType::ASSISTANT, $response->choices[0]['message']['role'] ?? null);
        $this->assertNotNull($response->choices[0]['message']['content'] ?? null);
    }

    public function testSuccessfullyMakesWebSearchInTheOpenRouteApiRequest(): void
    {
        OpenRouter::reset();
        $plugins = [
            new PluginData(
                id: 'web',
                max_results: 3,
            ),
        ];
        $chatData = new ChatData(
            messages: [
                new MessageData(
                    content: 'What are the latest developments in AI?',
                    role: RoleType::USER,
                ),
            ],
            model: $this->model,
            plugins: $plugins,
        );
        $mockBody = $this->mockBasicBody();
        $mockBody['choices'][0]['message']['annotations'] = [
            [
                'type' => 'url_citation',
                'url_citation' => [
                    'url' => 'https://example.com/ai-developments',
                    'title' => 'Latest Developments in AI',
                    'content' => 'This article discusses the latest advancements in artificial intelligence...',
                ],
            ],
        ];
        $this->mockOpenRouterFacade($mockBody);

        $response = OpenRouter::chat($chatData);

        $this->generalTestAssertions($response);
        $this->assertNotNull($response->choices[0]['message']['annotations'] ?? null);
        $this->assertEquals('url_citation', $response->choices[0]['message']['annotations'][0]['type'] ?? null);
    }

    public function testSuccessfullySendsFileContentInMessagesInTheOpenRouteApiRequest(): void
    {
        OpenRouter::reset();
        $plugins = [
            new PluginData(
                id: 'file-parser',
                pdf: [
                    'engine' => 'pdf-text',
                ],
            ),
        ];
        $fileContentData = new FileContentData(
            type: FileContentData::ALLOWED_TYPE,
            file: new FileUrlData(
                file_data: 'https://arxiv.org/pdf/1706.03762',
                filename: 'document.pdf',
            ),
        );
        $textContentData = new TextContentData(
            type: TextContentData::ALLOWED_TYPE,
            text: 'Please summarize this document.',
        );
        $messageData = new MessageData(
            content: [
                $textContentData,
                $fileContentData,
            ],
            role: RoleType::USER,
        );
        $chatData = new ChatData(
            messages: [$messageData],
            model: $this->model,
            plugins: $plugins,
        );
        $this->mockOpenRouterFacade($this->mockBasicBody());

        $response = OpenRouter::chat($chatData);

        $this->generalTestAssertions($response);
        $this->assertEquals(RoleType::ASSISTANT, $response->choices[0]['message']['role'] ?? null);
        $this->assertNotNull($response->choices[0]['message']['content'] ?? null);
    }

    public function testSendsImageAspectRatioForImageGenerationInTheOpenRouteApiRequest(): void
    {
        $chatData = new ChatData(
            messages: [
                new MessageData(
                    content: 'Generate a beautiful sunset over mountains',
                    role: RoleType::USER,
                ),
            ],
            model: 'google/gemini-2.5-flash-image-preview',
            modalities: ['image', 'text'],
            image_config: new ImageConfigData(
                aspect_ratio: '16:9',
            ),
        );
        $mockBody = $this->mockBasicBody();
        $mockBody['choices'][0]['message']['images'] = [
            [
                'image_url' => [
                    'url' => 'https://example.com/generated-image-1.jpg',
                ],
            ],
        ];
        $this->mockOpenRouter($mockBody);

        $response = $this->api->chatRequest($chatData);

        $this->generalTestAssertions($response);
        $this->assertNotNull($response->choices[0]['message']['images'] ?? null);
        $images = $response->choices[0]['message']['images'] ?? [];
        $this->assertIsArray($images);
        $this->assertNotEmpty($images);
    }

    public function testSuccessfullySendsAudioInContentInMessagesInTheOpenRouteApiRequest(): void
    {
        $data = base64_encode('fake-audio-data');
        $audioContentData = new AudioContentData(
            type: AudioContentData::ALLOWED_TYPE,
            input_audio: new InputAudioData(
                data: $data,
                format: AudioFormatType::MP3,
            ),
        );
        $messageData = new MessageData(
            content: [
                $audioContentData,
            ],
            role: RoleType::USER,
        );
        $chatData = new ChatData(
            messages: [
                $messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
        $this->mockOpenRouter($this->mockBasicBody());

        $response = $this->api->chatRequest($chatData);

        $this->generalTestAssertions($response);
        $this->assertEquals(RoleType::ASSISTANT, $response->choices[0]['message']['role'] ?? null);
        $this->assertNotNull($response->choices[0]['message']['content'] ?? null);
    }

    public function testMakesCostRequestWithGenerationId(): void
    {
        $chatData = new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
        $this->mockOpenRouter($this->mockBasicBody());
        $chatResponse = $this->api->chatRequest($chatData);
        $generationId = $chatResponse->id;
        $this->mockOpenRouter($this->mockBasicCostBody());

        $response = $this->api->costRequest($generationId);

        $this->assertInstanceOf(CostResponseData::class, $response);
        $this->assertNotNull($response->id);
        $this->assertEquals($this->model, $response->model);
        $this->assertNotNull($response->total_cost);
        $this->assertNotNull($response->origin);
    }

    public function testToArrayForCostRequestWorksAsExpected(): void
    {
        $chatData = new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
        $this->mockOpenRouter($this->mockBasicBody());
        $chatResponse = $this->api->chatRequest($chatData);
        $generationId = $chatResponse->id;
        $this->mockOpenRouter($this->mockBasicCostBody());

        $response = $this->api->costRequest($generationId);

        $this->assertInstanceOf(CostResponseData::class, $response);
        $responseArray = $response->toArray();
        $this->assertArrayHasKey('id', $responseArray);
        $this->assertArrayHasKey('model', $responseArray);
        $this->assertArrayHasKey('total_cost', $responseArray);
        $this->assertArrayHasKey('origin', $responseArray);
    }

    public function testMakesChatCompletionApiRequestWithLlmParameters(): void
    {
        $maxTokens = 250;
        $temperature = 1.2;
        $topP = 0.7;
        $topK = 1.2;
        $frequencyPenalty = 2;
        $presencePenalty = 1.2;
        $repetitionPenalty = 1;
        $seed = 2;
        $chatData = new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $maxTokens,
            temperature: $temperature,
            top_p: $topP,
            top_k: $topK,
            frequency_penalty: $frequencyPenalty,
            presence_penalty: $presencePenalty,
            repetition_penalty: $repetitionPenalty,
            seed: $seed,
        );
        $this->mockOpenRouter($this->mockBasicBody());

        $response = $this->api->chatRequest($chatData);

        $this->generalTestAssertions($response);
        $this->assertEquals(RoleType::ASSISTANT, $response->choices[0]['message']['role'] ?? null);
        $this->assertNotNull($response->choices[0]['message']['content'] ?? null);
    }

    public function testMakesChatCompletionApiRequestWithOpenRouterSpecificParameters(): void
    {
        $modelOpenchat = 'openchat/openchat-7b:free';
        $modelGryphe = 'gryphe/mythomist-7b:free';
        $transforms = ['middle-out'];
        $models = [$this->model, $modelOpenchat, $modelGryphe];
        $route = RouteType::FALLBACK;
        $provider = new ProviderPreferencesData(
            allow_fallbacks: true,
            require_parameters: true,
            data_collection: DataCollectionType::ALLOW,
        );
        $chatData = new ChatData(
            messages: [
                $this->messageData,
            ],
            max_tokens: $this->maxTokens,
            transforms: $transforms,
            models: $models,
            route: $route,
            provider: $provider,
        );
        $this->mockOpenRouter($this->mockBasicBody());

        $response = $this->api->chatRequest($chatData);

        $this->assertInstanceOf(ResponseData::class, $response);
        $this->assertNotNull($response->id);
        $this->assertEquals($this->model, $response->model);
        $this->assertEquals('chat.completion', $response->object);
    }

    public function testThrowsXorValidationExceptionWhenBothModelAndModelsEmptyInChatData(): void
    {
        $this->expectException(OpenRouterValidationException::class);

        new ChatData(
            messages: [
                $this->messageData,
            ],
            max_tokens: $this->maxTokens,
        );
    }

    public function testThrowsXorValidationExceptionWhenBothModelAndModelsAreProvided(): void
    {
        $modelGryphe = 'gryphe/mythomist-7b:free';
        $models = [$modelGryphe, $this->model];
        $this->expectException(OpenRouterValidationException::class);

        new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
            models: $models,
        );
    }

    public function testThrowsValidationExceptionWhenNotAllowedValueIsSentForRoute(): void
    {
        $route = 'random';
        $this->expectException(OpenRouterValidationException::class);

        new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
            route: $route,
        );
    }

    public function testMakesChatCompletionWithToolDefinition(): void
    {
        $tools = [
            new ToolCallData(
                type: 'function',
                function: new FunctionData(
                    name: 'getWeather',
                    description: 'Get the current weather for a location',
                    parameters: [
                        'type' => 'object',
                        'properties' => [
                            'location' => [
                                'type' => 'string',
                                'description' => 'The city name',
                            ],
                        ],
                        'required' => ['location'],
                    ],
                ),
            ),
        ];
        $chatData = new ChatData(
            messages: [
                new MessageData(
                    content: 'What is the weather like in Tokyo?',
                    role: RoleType::USER,
                ),
            ],
            model: 'mistralai/devstral-2512:free',
            max_tokens: $this->maxTokens,
            tool_choice: ToolChoiceType::AUTO,
            tools: $tools,
        );
        $mockBody = $this->mockBasicBody();
        $mockBody['choices'][0]['message']['content'] = null;
        $mockBody['choices'][0]['message']['tool_calls'] = [
            [
                'id' => 'call_7F3kP9',
                'type' => 'function',
                'function' => [
                    'name' => 'getWeather',
                    'arguments' => '{"location": "Tokyo"}',
                ],
            ],
        ];
        $mockBody['choices'][0]['finish_reason'] = 'tool_calls';
        $this->mockOpenRouter($mockBody);

        $response = $this->api->chatRequest($chatData);

        $this->generalTestAssertions($response);
        $this->assertEquals(RoleType::ASSISTANT, $response->choices[0]['message']['role'] ?? null);
        $this->assertNotNull($response->choices[0]['message']['tool_calls'] ?? null);
        $this->assertEquals('getWeather', $response->choices[0]['message']['tool_calls'][0]['function']['name'] ?? null);
    }

    public function testThrowsValidationExceptionWhenNotAllowedValueIsSentForToolChoice(): void
    {
        $toolChoice = 'random';
        $this->expectException(OpenRouterValidationException::class);

        new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
            tool_choice: $toolChoice,
        );
    }

    public function testMakesLimitOpenRouteApiRequestAndGetsRateLimitAndCreditLeftOnApiKey(): void
    {
        $this->mockOpenRouter($this->mockBasicLimitBody());

        $response = $this->api->limitRequest();

        $this->assertInstanceOf(LimitResponseData::class, $response);
        $this->assertNotNull($response->label);
        $this->assertNotNull($response->usage);
        $this->assertNotNull($response->is_free_tier);
        $this->assertNotNull($response->limit);
        $this->assertNotNull($response->limit_remaining);
        $this->assertNotNull($response->rate_limit);
        $this->assertNotNull($response->rate_limit->requests);
        $this->assertNotNull($response->rate_limit->interval);
    }

    public function testToArrayForLimitRequestWorkingAsExpected(): void
    {
        $this->mockOpenRouter($this->mockBasicLimitBody());

        $response = $this->api->limitRequest();

        $this->assertInstanceOf(LimitResponseData::class, $response);
        $responseArray = $response->toArray();
        $this->assertIsArray($responseArray);
        $this->assertArrayHasKey('label', $responseArray);
        $this->assertArrayHasKey('usage', $responseArray);
        $this->assertArrayHasKey('is_free_tier', $responseArray);
        $this->assertArrayHasKey('limit', $responseArray);
        $this->assertArrayHasKey('limit_remaining', $responseArray);
        $this->assertArrayHasKey('rate_limit', $responseArray);
        $this->assertIsArray($responseArray['rate_limit']);
    }

    public function testMakesOpenRouteApiRequestByUsingFacade(): void
    {
        OpenRouter::reset();
        $chatData = new ChatData(
            messages: [
                $this->messageData,
            ],
            model: $this->model,
            max_tokens: $this->maxTokens,
        );
        $this->mockOpenRouterFacade($this->mockBasicBody());

        $response = OpenRouter::chat($chatData);

        $this->generalTestAssertions($response);
    }
}
