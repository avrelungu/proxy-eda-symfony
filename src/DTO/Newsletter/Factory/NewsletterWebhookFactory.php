<?php

namespace App\DTO\Newsletter\Factory;

use App\DTO\Newsletter\NewsletterWebhook;
use App\DTO\WebhookDTO;
use App\Webhook\Error\Exception\WebhookException;
use Symfony\Component\Serializer\SerializerInterface;

class NewsletterWebhookFactory
{
    public function __construct(
        private SerializerInterface $serializer
    )
    {
    }

    public function create(WebhookDTO $webhook): NewsletterWebhook
    {
        try {
            $newsletterWebhook = $this->serializer->deserialize(
                $webhook->getRawPayload(),
                NewsletterWebhook::class,
                'json'
            );

            return $newsletterWebhook;
        } catch (\Throwable $exception) {
            throw new WebhookException(
                'Unable to create NewsletterWebhook: ' . $exception->getMessage()
            );
        }
    }
}