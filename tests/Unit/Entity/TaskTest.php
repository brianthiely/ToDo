<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\Users;
use App\Tests\Unit\AbstractTestCase;
use DateTimeImmutable;

class TaskTest extends AbstractTestCase
{
    private const TITLE_NOT_BLANK_CONSTRAINT_MESSAGE = 'Vous devez saisir un titre.';
    private const CONTENT_NOT_BLANK_CONSTRAINT_MESSAGE = 'Vous devez saisir du contenu.';
    private const INVALID_TITLE_VALUE = '';
    private const INVALID_CONTENT_VALUE = '';
    private const VALID_TITLE_VALUE = 'title';
    private const VALID_CONTENT_VALUE = 'content';


    public function testTaskGettersAndSetters()
    {
        $task = new Task();
        $propertyValues = [
            'title' => self::VALID_TITLE_VALUE,
            'content' => self::VALID_CONTENT_VALUE,
            'user' => new Users()
        ];

        $this->testGettersAndSetters($task, $propertyValues);

        $task->setCreatedAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $task->getCreatedAt());

    }


    public function testIsDone(): void
    {
        $task = new Task();
        $this->assertFalse($task->isDone());
        $task->toggle(true);
        $this->assertTrue($task->isDone());
    }

    public function testUserAssociation(): void
    {
        $task = new Task();

        $user = new Users();
        $user->setUsername('username')
            ->setEmail('user@test.com')
            ->setPassword('password')
            ->setRoles(['ROLE_USER']);

        $task->setUser($user);
        $this->assertSame($user, $task->getUser());
    }

    public function testTaskEntityIsValid()
    {
        $task = new Task();

        $task->setTitle(self::VALID_TITLE_VALUE)
            ->setContent(self::VALID_CONTENT_VALUE)
            ->setUser(new Users())
            ->toggle(true);

        $this->getValidationErrors($task);
    }

    public function testTaskEntityInvalidTitleEntered()
    {
        $task = new Task();

        $task->setTitle(self::INVALID_TITLE_VALUE)
            ->setContent(self::VALID_CONTENT_VALUE)
            ->setUser(new Users())
            ->toggle(true);

        $errors = $this->validator->validate($task);

        $this->assertEquals(self::TITLE_NOT_BLANK_CONSTRAINT_MESSAGE, $errors[0]->getMessage());
    }

    public function testTaskEntityInvalidContentEntered()
    {
        $task = new Task();

        $task->setTitle(self::VALID_TITLE_VALUE)
            ->setContent(self::INVALID_CONTENT_VALUE)
            ->setUser(new Users())
            ->toggle(true);

        $errors = $this->validator->validate($task);

        $this->assertEquals(self::CONTENT_NOT_BLANK_CONSTRAINT_MESSAGE, $errors[0]->getMessage());
    }

}