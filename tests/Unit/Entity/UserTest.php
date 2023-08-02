<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Tests\Unit\AbstractTestCase;

class UserTest extends AbstractTestCase
{
    private const USERNAME_NOT_BLANK_CONSTRAINT_MESSAGE = 'Vous devez saisir un nom d\'utilisateur.';
    private const EMAIL_NOT_BLANK_CONSTRAINT_MESSAGE = 'Vous devez saisir une adresse email.';
    private const INVALID_EMAIL_VALUE = '';
    private const INVALID_USERNAME_VALUE = '';
    private const VALID_EMAIL_VALUE = 'user@test.com';
    private const VALID_USERNAME_VALUE = 'username';
    private const VALID_PASSWORD_VALUE = 'password';
    private const USER_ROLES = ['ROLE_USER'];
    private const ADMIN_ROLES = ['ROLE_ADMIN'];


    public function testUserGettersAndSetters()
    {
        $user = new User();
        $propertyValues = [
            'username' => self::VALID_USERNAME_VALUE,
            'email' => self::VALID_EMAIL_VALUE,
            'password' => self::VALID_PASSWORD_VALUE,
            'roles' => self::USER_ROLES
        ];

        $this->testGettersAndSetters($user, $propertyValues);
    }

    public function testUserEntityIsValid()
    {
        $user = new User();

        $user->setUsername(self::VALID_USERNAME_VALUE)
            ->setEmail(self::VALID_EMAIL_VALUE)
            ->setPassword(self::VALID_PASSWORD_VALUE)
            ->setRoles(self::USER_ROLES);

       $this->getValidationErrors($user);

    }

    public function testUserEntityInvalidEmailEntered()
    {
        $user = new User();

        $user->setUsername(self::VALID_USERNAME_VALUE)
            ->setEmail(self::INVALID_EMAIL_VALUE)
            ->setPassword(self::VALID_PASSWORD_VALUE)
            ->setRoles(self::USER_ROLES);

        $errors = $this->validator->validate($user);

        $this->assertEquals(self::EMAIL_NOT_BLANK_CONSTRAINT_MESSAGE, $errors[0]->getMessage());
    }

    public function testUserEntityInvalidUsernameEntered()
    {
        $user = new User();

        $user->setUsername(self::INVALID_USERNAME_VALUE)
            ->setEmail(self::VALID_EMAIL_VALUE)
            ->setPassword(self::VALID_PASSWORD_VALUE)
            ->setRoles(self::USER_ROLES);

        $errors = $this->validator->validate($user);

        $this->assertEquals(self::USERNAME_NOT_BLANK_CONSTRAINT_MESSAGE, $errors[0]->getMessage());
    }

    public function testIsAdmin(): void
    {
        $user = new User();
        $user->setRoles(self::ADMIN_ROLES);

        $this->assertTrue($user->isAdmin());
    }

    public function testIsNotAdmin(): void
    {
        $user = new User();
        $user->setRoles(self::USER_ROLES);

        $this->assertFalse($user->isAdmin());
    }



}
