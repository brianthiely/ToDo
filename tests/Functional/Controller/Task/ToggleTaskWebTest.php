<?php

namespace App\Tests\Functional\Controller\Task;

use App\Entity\Task;
use App\Tests\Functional\AbstractWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ToggleTaskWebTest extends AbstractWebTestCase
{

    /**
     * @throws Exception
     */
    public function testToggleTaskSuccessByOwner(): void
    {
        $this->loginUser('user');

        $user = $this->getLoggedInUser();
        $userId = $user->getId();

        $taskRepository = $this->getEntityManager()->getRepository(Task::class);
        $taskForCurrentUser = $taskRepository->findOneBy(['User' => $userId]);

        $this->accessPage('task_toggle', ['id' => $taskForCurrentUser->getId()]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert.alert-success','La tâche '.$taskForCurrentUser->getTitle().' a bien été marquée comme faite.');

        $updatedTask = $taskRepository->findOneBy(['id' => $taskForCurrentUser->getId()]);

        $this->assertTrue($updatedTask->isDone());
    }

    /**
     * @throws Exception
     */
    public function testToggleTaskUnauthorizedUser(): void
    {
        $this->loginUser('user');
        $userId = $this->getLoggedInUser()->getId();

        $taskRepository = $this->getEntityManager()->getRepository(Task::class);
        $taskForOtherUser = $taskRepository->findOneBy(['User' => $userId + 1]);

        $this->accessPage('task_toggle', ['id' => $taskForOtherUser->getId()]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }

    public function testToggleTaskUnauthenticatedUser(): void
    {
        $this->accessPage('task_toggle', ['id' => 41]);
        $this->assertResponseRedirects('http://localhost/login');

    }


}