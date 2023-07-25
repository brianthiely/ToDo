<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $passwordEncoder)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $userData = [
            ['username' => 'admin', 'email' => 'admin@mail.com', 'password' => 'admin', 'roles' => ['ROLE_ADMIN']],
            ['username' => 'admin2', 'email' => 'admin2@mail.com', 'password' => 'admin2', 'roles' => ['ROLE_ADMIN']],
            ['username' => 'user', 'email' => 'user@mail.com', 'password' => 'user', 'roles' => ['ROLE_USER']],
            ['username' => 'user2', 'email' => 'user2@mail.com', 'password' => 'user2', 'roles' => ['ROLE_USER']],
        ];

        foreach ($userData as $data) {
            $user = new User();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPassword($this->passwordEncoder->hashPassword($user, $data['password']));
            $user->setRoles($data['roles']);
            $manager->persist($user);
        }

        $manager->flush();
    }

}
