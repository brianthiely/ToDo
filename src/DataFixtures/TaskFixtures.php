<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $task = new Task();
        $task->setTitle('Task 1');
        $task->setContent('Content 1');
        $manager->persist($task);


        $manager->flush();
    }
}
