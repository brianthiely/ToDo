<?php

namespace App\Tests\Functional\Controller\Task;

use App\Entity\Task;
use App\Entity\Users;
use App\Tests\Functional\AbstractWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class DeleteTaskWebTest extends AbstractWebTestCase
{

    /**
     * @throws Exception
     */
    public function testDeleteTaskByOwner(): void
    {
        $this->loginUser('user');
        $userId = $this->getLoggedInUser()->getId();

        $task = $this->getEntityManager()
            ->getRepository(Task::class)
            ->findOneBy(['User' => $userId]);

        $this->assertSame($userId, $task->getUser()->getId());

        $this->accessPage('task_delete', ['id' => $task->getId()]);
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche a bien été supprimée.');

    }

    /**
     * @throws Exception
     */
    public function testDeleteTaskAnonymousByAdmin(): void
    {
        $this->loginUser('admin');
        $adminUser = $this->getLoggedInUser();

        $anonymousUser = $this->getEntityManager()
            ->getRepository(Users::class)
            ->findOneBy(['username' => 'Anonyme']);

        $tasksAnonymous = $this->getEntityManager()
            ->getRepository(Task::class)
            ->findOneBy(['User' => $anonymousUser->getId()]);

        $this->assertTrue($tasksAnonymous->getUser() === $anonymousUser && $adminUser->isAdmin());

        $this->accessPage('task_delete', ['id' => $tasksAnonymous->getId()]);
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche a bien été supprimée.');

    }

    /**
     * @throws Exception
     */
    public function testDeleteTaskUnauthorized(): void
    {
        $this->loginUser('user');
        $userId = $this->getLoggedInUser()->getId();

        $task = $this->getEntityManager()
            ->getRepository(Task::class)
            ->findOneBy(['User' => $userId + 1]);

        $this->assertNotSame($userId, $task->getUser()->getId());
        $this->accessPage('task_delete', ['id' => $task->getId()]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

}