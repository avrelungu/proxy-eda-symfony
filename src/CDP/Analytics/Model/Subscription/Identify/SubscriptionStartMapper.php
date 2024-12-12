<?php

namespace App\CDP\Analytics\Model\Subscription\Identify;

use App\CDP\Analytics\Model\Subscription\SubscriptionSourceInterface;
use App\Webhook\Error\Exception\WebhookException;

class SubscriptionStartMapper
{
    public function map(SubscriptionSourceInterface $source, IdentifyModel $target)
    {
        try {
            $target->setProduct($source->getProduct());
            $target->setEventDate($source->getEventDate());
            $target->setSubscriptionId($source->getSubscriptionId());
            $target->setEmail($source->getEmail());
            $target->setId($source->getUserId());
        } catch (\Throwable $exception) {
            $className = get_class($source);

            throw new WebhookException("Could not map $className to IdentifyModel target: " . $exception->getMessage());
        }
    }
}