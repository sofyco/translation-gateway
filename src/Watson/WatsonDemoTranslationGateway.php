<?php declare(strict_types=1);

namespace Sofyco\Translation\Gateway\Watson;

use Sofyco\Translation\Gateway\TranslationGatewayInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class WatsonDemoTranslationGateway implements TranslationGatewayInterface
{
    private const string DETECT_URl = 'https://www.ibm.com/demos/live/watson-language-translator/api/translate/detect';
    private const string TRANSLATE_URl = 'https://www.ibm.com/demos/live/watson-language-translator/api/translate/text';

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function detect(string $text): ?string
    {
        $data = $this->sendRequest(self::DETECT_URl, ['text' => $text]);

        return $data['payload']['languages'][0]['language']['language'] ?? null;
    }

    public function translate(string $text, string $target, ?string $source = null): ?string
    {
        if (null === $source) {
            $source = $this->detect($text);
        }

        if (null === $source) {
            return null;
        }

        $data = $this->sendRequest(self::TRANSLATE_URl, ['text' => $text, 'source' => $source, 'target' => $target]);

        return $data['payload']['translations'][0]['translation'] ?? null;
    }

    private function sendRequest(string $url, array $data): ?array
    {
        try {
            $response = $this->httpClient->request(Request::METHOD_POST, $url, [
                'json' => $data,
            ]);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                return null;
            }

            return $response->toArray();
        } catch (\Throwable $throwable) {
            return null;
        }
    }
}
