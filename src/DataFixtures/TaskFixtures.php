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

        $task->setTitle('Task User')
            ->setContent('OK')
            ->setUser($this->getReference('user'));

        $task->setTitle('Task User 2')
            ->setContent('OK')
            ->setUser($this->getReference('user2'));

        $manager->persist($task);
        $manager->flush();
    }
}
