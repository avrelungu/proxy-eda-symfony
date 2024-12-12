<?php

namespace App\Controller;

use App\DTO\WebhookDTO;
use App\Webhook\Handler\HandlerDelegator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class WebhookController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private HandlerDelegator $handleDelegator
    ) {
    }

    #[Route(path: '/webhook', name: 'webhook', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        try {
            $webhookDTO = $this->serializer->deserialize($request->getContent(), WebhookDTO::class, 'json');
            $webhookDTO->setRawPayload($request->getContent());
            $this->handleDelegator->delegate($webhookDTO);

            return new Response(status: 204);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }
}
