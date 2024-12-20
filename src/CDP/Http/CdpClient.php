<?php

namespace App\CDP\Http;

use App\CDP\Analytics\Model\ModelInterface;
use App\Webhook\Error\Exception\WebhookException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CdpClient implements CdpClientInterface
{
    private const string CDP_API_URL = 'https://some-cdp-api.com';

    public function __construct(
        private HttpClientInterface $httpClient, // HttpClient
        #[Autowire('%cdp.api_key')] private string $apiKey
    ) {
    }

    public function track(ModelInterface $trackModel): void
    {
        $response = $this->httpClient->request(
            'POST',
            self::CDP_API_URL . '/track',
            [
                'body' => json_encode($trackModel->toArray(), JSON_THROW_ON_ERROR),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'API-KEY' => $this->apiKey
                ]
            ]
        );

        // Add error handling

        try {
            $response->toArray();
        } catch (\Throwable $exception) {
            throw new WebhookException(
                message: $response->getContent(false),
                previous: $exception
            );
        }
    }

    public function identify(ModelInterface $identifyModel): void
    {
        $response = $this->httpClient->request(
            'POST',
            self::CDP_API_URL . '/identify',
            [
                'body' => json_encode($identifyModel->toArray(), JSON_THROW_ON_ERROR),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'API-KEY' => $this->apiKey
                ]
            ]
        );

        // Add error handling
        try {
            $response->toArray();
        } catch (\Throwable $exception) {
            throw new WebhookException(
                message: $response->getContent(false),
                previous: $exception
            );
        }
    }
}
