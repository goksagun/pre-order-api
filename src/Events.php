<?php

namespace App;

/**
 * This class defines the names of all the events dispatched in
 * the Symfony application.
 */
final class Events
{
    /**
     * For the event naming conventions, see:
     * https://symfony.com/doc/current/components/event_dispatcher.html#naming-conventions.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    public const ORDER_CHANGED = 'order.changed';
}