<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;



class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 5; $i++) {
            $task = new Task();
            $task->setTitle('Tâche n°' . $i);
            $task->setContent('Contenu de la tâche n°' . $i);
        }

        $user1 = $manager->getRepository(Users::class)->findOneBy(['username' => 'user']);
        $user2 = $manager->getRepository(Users::class)->findOneBy(['username' => 'user2']);

        $task->setTitle('Task User')
            ->setContent('OK')
            ->setUser($user1->getId());

        $task->setTitle('Task User 2')
            ->setContent('OK')
            ->setUser($user2->getId());

        $manager->persist($task);
        $manager->flush();
    }
}
