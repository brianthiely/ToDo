<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $passwordEncoder)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadTasks($manager);
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $userData = [
            ['username' => 'admin', 'email' => 'admin@mail.com', 'password' => 'admin', 'roles' => ['ROLE_ADMIN']],
            ['username' => 'admin2', 'email' => 'admin2@mail.com', 'password' => 'admin2', 'roles' => ['ROLE_ADMIN']],
            ['username' => 'user', 'email' => 'user@mail.com', 'password' => 'user', 'roles' => ['ROLE_USER']],
            ['username' => 'user2', 'email' => 'user2@mail.com', 'password' => 'user2', 'roles' => ['ROLE_USER']],
        ];

        foreach ($userData as $data) {
            $user = new Users();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPassword($this->passwordEncoder->hashPassword($user, $data['password']));
            $user->setRoles($data['roles']);
            $manager->persist($user);
        }

        $manager->flush();
    }

    private function loadTasks(ObjectManager $manager): void
    {
        $users = [
            'user' => $manager->getRepository(Users::class)->findOneBy(['username' => 'user']),
            'user2' => $manager->getRepository(Users::class)->findOneBy(['username' => 'user2']),
        ];

        for ($i = 0; $i < 5; $i++) {
            $task = new Task();
            $task->setTitle('Tâche n°' . $i);
            $task->setContent('Contenu de la tâche n°' . $i);
            $manager->persist($task);
        }

        $taskUser1 = new Task();
        $taskUser1->setTitle('Task User')
            ->setContent('OK')
            ->setUser($users['user']);
        $manager->persist($taskUser1);

        $taskUser2 = new Task();
        $taskUser2->setTitle('Task User 2')
            ->setContent('OK')
            ->setUser($users['user2']);
        $manager->persist($taskUser2);

        $manager->flush();
    }

}