<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Task;
use App\Entity\Users;
use App\Tests\Functional\AbstractWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerWebTest extends AbstractWebTestCase
{
    /**
     * @throws Exception
     */
    public function testListTaskIsSuccessForUser()
    {
        // Se connecter en tant qu'utilisateur "user"
        $this->loginUser('user');

        // Accéder à la page de liste des tâches
        $crawler = $this->accessPage('task_list');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Récupérer la liste des tâches depuis la base de données
        $taskRepository = $this->getEntityManager()->getRepository(Task::class);
        $tasks = $taskRepository->findAll();

        // Vérifier que chaque tâche dans la liste est bien affichée sur la page
        foreach ($tasks as $task) {
            $this->assertStringContainsString($task->getTitle(), $crawler->html());
            $this->assertStringContainsString($task->getContent(), $crawler->html());
        }
    }

    /**
     * @throws Exception
     */
    public function testListTaskIsSuccessForAdmin()
    {
        $this->loginUser('admin');

        $crawler = $this->accessPage('task_list');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $taskRepository = $this->getEntityManager()->getRepository(Task::class);
        $tasks = $taskRepository->findAll();

        foreach ($tasks as $task) {
            $this->assertStringContainsString($task->getTitle(), $crawler->html());
            $this->assertStringContainsString($task->getContent(), $crawler->html());
        }
    }

    public function testListTaskForbiddenForAnonymous()
    {
        $this->accessPage('task_list');
        $redirectUrl = $this->client->getResponse()->headers->get('location');
        $this->assertEquals('/login', parse_url($redirectUrl, PHP_URL_PATH));
    }

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

        $redirectUrl = $this->client->getResponse()->headers->get('location');
        $this->assertEquals('/login', parse_url($redirectUrl, PHP_URL_PATH));

    }

    /**
     * @throws Exception
     */
    public function testEditTaskSuccessByOwner(): void
    {
        $this->loginUser('user');

        $user = $this->getLoggedInUser();
        $userId = $user->getId();

        $taskRepository = $this->getEntityManager()->getRepository(Task::class);
        $taskForCurrentUser = $taskRepository->findOneBy(['User' => $userId]);

        $crawler = $this->accessPage('task_edit', ['id' => $taskForCurrentUser->getId()]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérifier que les champs du formulaire contiennent les bonnes valeurs
        $this->assertSame('Task User', $crawler
            	->filter('input[name="task[title]"]')
                ->attr('value'));
        $this->assertSame('OK', $crawler
                ->filter('textarea[name="task[content]"]')
                ->text());

        // Soumettre le formulaire d'édition avec de nouvelles valeurs
        $this->submitForm($crawler, 'Modifier', [
            'task[title]' => 'Tâche de test modifiée',
            'task[content]' => 'Contenu de la tâche de test modifiée'
        ]);

        $this->assertSelectorTextContains('div.alert.alert-success','La tâche a bien été modifiée.');

        $updatedTask = $taskRepository->findOneBy(['title' => 'Tâche de test modifiée']);

        $this->assertNotSame('Task User', $updatedTask->getTitle());
        $this->assertNotSame('OK', $updatedTask->getContent());

    }

    /**
     * @throws Exception
     */
    public function testEditTaskUnauthorizedUser(): void
    {
        $this->loginUser('user');

        $taskRepository = $this->getEntityManager()->getRepository(Task::class);
        $taskForOtherUser = $taskRepository->findOneBy(['User' => 21]);

        if ($taskForOtherUser) {
            $this->accessPage('task_edit', ['id' => $taskForOtherUser->getId()]);
            $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);}

    }

    public function testEditTaskUnauthenticatedUser(): void
    {
        $this->accessPage('task_edit', ['id' => 63]);

        $redirectUrl = $this->client->getResponse()->headers->get('location');
        $this->assertEquals('/login', parse_url($redirectUrl, PHP_URL_PATH));

    }

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

        $redirectUrl = $this->client->getResponse()->headers->get('location');
        $this->assertEquals('/login', parse_url($redirectUrl, PHP_URL_PATH));
    }

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