<?php

namespace App\EventListener;

use App\Entity\SoftDeleteInterface;
use Doctrine\ORM\Event\PreFlushEventArgs;

class DoctrineListener
{
    public function preFlush(PreFlushEventArgs $event)
    {
        $em = $event->getEntityManager();

        foreach ($em->getUnitOfWork()->getScheduledEntityDeletions() as $object) {
            $reflectedObject = new \ReflectionObject($object);

            if ($reflectedObject->implementsInterface(SoftDeleteInterface::class)) {
                if ($object->getDeletedAt() instanceof \DateTimeInterface) {
                    continue;
                }

                $object->setDeletedAt(new \DateTimeImmutable());

                $em->merge($object);
                $em->persist($object);
            }
        }
    }
}