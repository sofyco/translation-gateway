<?php declare(strict_types=1);

namespace Sofyco\Translation\Gateway\Tests\Watson;

use PHPUnit\Framework\TestCase;
use Sofyco\Translation\Gateway\Watson\WatsonDemoTranslationGateway;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class WatsonDemoTranslationGatewayTest extends TestCase
{
    public function testDetect(): void
    {
        $response = self::createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn([
            'payload' => [
                'languages' =>      [
                    [
                        'language' => [
                            'language' => 'en',
                        ],
                    ],
                ],
            ],
        ]);

        $httpClient = self::createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $gateway = new WatsonDemoTranslationGateway(httpClient: $httpClient);

        $result = $gateway->detect(text: 'One');

        self::assertSame('en', $result);
    }

    public function testTranslate(): void
    {
        $response = self::createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('toArray')->willReturn([
            'payload' => [
                'translations' => [
                    [
                        'translation' => 'Один',
                    ],
                ],
            ],
        ]);

        $httpClient = self::createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $gateway = new WatsonDemoTranslationGateway(httpClient: $httpClient);

        $result = $gateway->translate(text: 'One', target: 'uk', source: 'en');

        self::assertSame('Один', $result);
    }

    public function testTranslateWithoutSourceArgument(): void
    {
        $detectResponse = self::createMock(ResponseInterface::class);
        $detectResponse->method('getStatusCode')->willReturn(200);
        $detectResponse->method('toArray')->willReturn([
            'payload' => [
                'languages' => [
                    [
                        'language' => [
                            'language' => 'en',
                        ],
                    ],
                ],
            ],
        ]);

        $translateResponse = self::createMock(ResponseInterface::class);
        $translateResponse->method('getStatusCode')->willReturn(200);
        $translateResponse->method('toArray')->willReturn([
            'payload' => [
                'translations' => [
                    [
                        'translation' => 'Один',
                    ],
                ],
            ],
        ]);

        $httpClient = self::createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturnOnConsecutiveCalls($detectResponse, $translateResponse);

        $gateway = new WatsonDemoTranslationGateway(httpClient: $httpClient);

        $result = $gateway->translate(text: 'One', target: 'uk');

        self::assertSame('Один', $result);
    }
}
