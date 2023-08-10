<?php

namespace App\Tests\Functional\Controller\Task;

use App\Entity\Task;
use App\Tests\Functional\AbstractWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateTaskWebTest extends AbstractWebTestCase
{

    /**
     * @throws Exception
     */
    public function testCreateTaskSuccess(): void
    {
        $this->loginUser('user');

        $crawler = $this->accessPage('task_create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->submitForm($crawler, 'Ajouter', [
            'task[title]' => 'Tâche de test',
            'task[content]' => 'Contenu de la tâche de test',
        ]);

        $this->assertSelectorTextContains('div.alert.alert-success','La tâche a été bien été ajoutée.');

        $task = $this->getEntityManager()
            ->getRepository(Task::class)
            ->findOneBy(['title' => 'Tâche de test']);

        $this->assertSame('Tâche de test', $task->getTitle());

        $loggedInUser = $this->getLoggedInUser();
        $this->assertSame($loggedInUser->getId(), $task->getUser()->getId());

    }

    public function testCreateTaskUnauthenticatedUser(): void
    {
        $this->accessPage('task_create');
        $this->assertResponseRedirects('http://localhost/login');

    }

}