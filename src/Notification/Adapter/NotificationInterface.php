<?php

namespace App\Notification\Adapter;

interface NotificationInterface
{
    public function send($to, $message): bool;
}