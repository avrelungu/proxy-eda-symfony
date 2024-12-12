<?php

namespace App\Webhook\Handler;

use App\DTO\WebhookDTO;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class HandlerDelegator
{
    /**
     * @param iterable<WebhookHandlerInterface> $handlers
     */
    public function __construct(
        #[AutowireIterator('webhook.handler')] private iterable $handlers
    ) {
    }

    public function delegate(WebhookDTO $webhookDTO): void
    {
        // loop over handler
        foreach ($this->handlers as $handler) {
            // ask if supported and handle if true
            if ($handler->supports($webhookDTO)) {
                $handler->handle($webhookDTO);
            }
        }
    }
}
