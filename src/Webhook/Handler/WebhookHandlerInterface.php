<?php

namespace App\Webhook\Handler;

use App\DTO\WebhookDTO;

interface WebhookHandlerInterface
{
    public function supports(WebhookDTO $webhook): bool;

    public function handle(WebhookDTO $webhook): void;
}
