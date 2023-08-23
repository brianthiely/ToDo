<?php

namespace App\EventSubscriber;

use App\Entity\Users;
use Doctrine\ORM\Events;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordEncoderSubscriber implements EventSubscriberInterface
{

    public function __construct(private readonly UserPasswordHasherInterface $encodePassword)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->encodeUserPassword($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->encodeUserPassword($args);
    }

    private function encodeUserPassword(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Users) {
            return;
        }

        $entity->setPassword($this->encodePassword->hashPassword($entity, $entity->getPassword()));
    }
}
