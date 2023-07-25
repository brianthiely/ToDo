<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;



class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $taskWithoutUser = new Task();
        $taskWithoutUser->setTitle('Tâche sans utilisateur');
        $taskWithoutUser->setContent('Contenu de la tâche sans utilisateur');
        $taskWithoutUser->setUser(null);
        $manager->persist($taskWithoutUser);

        $manager->flush();
    }
}
