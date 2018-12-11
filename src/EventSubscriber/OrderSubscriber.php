<?php

namespace App\EventSubscriber;

use App\Events;
use App\Notification\SmsNotification;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class OrderSubscriber implements EventSubscriberInterface
{
    private $sender;

    public function __construct(SmsNotification $sender)
    {
        $this->sender = $sender;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::ORDER_CHANGED => 'onOrderChanged',
        ];
    }

    public function onOrderChanged(GenericEvent $event)
    {
        $order = $event->getSubject();

        $this->sender->send(
            $order->getPhone(),
            sprintf(
                'Your order with id #%s status changed to \'%s\'',
                $order->getId(),
                $order->getStatus()
            )
        );
    }
}