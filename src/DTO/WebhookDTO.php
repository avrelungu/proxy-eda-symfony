<?php

namespace App\DTO;

class WebhookDTO
{
    private string $event;

    private string $rawPayload;

    /**
     * Get the value of rawPayload
     */
    public function getRawPayload(): string
    {
        return $this->rawPayload;
    }

    /**
     * Set the value of rawPayload
     *
     * @return  self
     */
    public function setRawPayload(string $rawPayload): WebhookDTO
    {
        $this->rawPayload = $rawPayload;

        return $this;
    }

    /**
     * Get the value of event
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * Set the value of event
     *
     * @return  self
     */
    public function setEvent(string $event): WebhookDTO
    {
        $this->event = $event;

        return $this;
    }
}
