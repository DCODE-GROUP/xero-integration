<?php

namespace Dcodegroup\XeroIntegration\Enums;

enum XeroWebhookStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SUCCESSFUL = 'successful';
    case FAILURE = 'failure';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
