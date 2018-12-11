<?php

namespace App\Notification;

use App\Notification\Adapter\NotificationInterface;
use App\Notification\Adapter\TwilioAdapter;

class SmsNotification implements NotificationInterface
{
    private $adapter;

    public function __construct(TwilioAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function send($to, $message): bool
    {
        return $this->adapter->send($to, $message);
    }
}