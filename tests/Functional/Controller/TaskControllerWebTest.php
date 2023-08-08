<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Task;
use App\Entity\Users;
use App\Tests\Functional\AbstractWebTestCase;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerWebTest extends AbstractWebTestCase
{
    /**
     * @throws Exception
     */
    public function testDeleteTaskSuccess()
    {
        $this->loginUser('user');
        $user = $this->getLoggedInUser();

        $this->loginUser('admin');
        $adminUser = $this->getLoggedInUser();

        $crawler = $this->accessPage('task_list');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);


        $this->testDeleteTaskByOwner($crawler, $user);
        $this->testDeleteTaskByAdmin($crawler, $adminUser);
        $this->testDeleteTaskUnauthorized($user);
    }

    /**
     * @throws Exception
     */
    public function testListTask()
    {
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
    public function testCreateTaskSuccess(): void
    {
        // Se connecter en tant qu'utilisateur "user"
        $this->loginUser('user');

        // Accéder à la page de création de tâche
        $crawler = $this->accessPage('task_create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Soumettre le formulaire de création de tâche avec des valeurs de test
        $this->submitForm($crawler, 'Ajouter', [
            'task[title]' => 'Tâche de test',
            'task[content]' => 'Contenu de la tâche de test',
        ]);

        // Vérifier qu'un message de succès est affiché après la soumission du formulaire
        $this->assertSelectorTextContains('div.alert.alert-success','La tâche a été bien été ajoutée.');

        // Récupérer la tâche depuis la base de données pour vérifier qu'elle a été correctement enregistrée
        $task = $this->getEntityManager()
            ->getRepository(Task::class)
            ->findOneBy(['title' => 'Tâche de test']);

        // Vérifier que la tâche a bien été enregistrée avec le titre soumis dans le formulaire
        $this->assertSame('Tâche de test', $task->getTitle());

        // Vérifier que la tâche est bien rattachée à l'utilisateur connecté (utilisateur "user")
        $loggedInUser = $this->getLoggedInUser();
        $this->assertSame($loggedInUser->getId(), $task->getUser()->getId());

    }

    public function testCreateTaskUnauthenticatedUser(): void
    {
        $this->accessPage('task_create');

        $this->assertResponseRedirects('/login');

        $crawler = $this->client->followRedirect();

        $this->submitForm($crawler, 'Ajouter', [
            'task[title]' => 'Tâche de test',
            'task[content]' => 'Contenu de la tâche de test',
        ]);

        $this->assertResponseRedirects('/login');

    }

    /**
     * @throws Exception
     */
    public function testEditTaskSuccess(): void
    {
        // Se connecter en tant qu'utilisateur "user"
        $this->loginUser('user');

        // Récupérer l'utilisateur connecté
        $user = $this->getLoggedInUser();
        $userId = $user->getId();

        // Récupérer une tâche associée à l'utilisateur connecté
        $taskRepository = $this->getEntityManager()->getRepository(Task::class);
        $taskForCurrentUser = $taskRepository->findOneBy(['User' => $userId]);

        // Vérifier que l'accès à l'URL d'édition de la tâche renvoie un code 200
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

        // Vérifier que la tâche a été modifiée avec succès
        $this->assertSelectorTextContains('div.alert.alert-success','La tâche a bien été modifiée.');

        // Récupérer la tâche mise à jour depuis la base de données
        $updatedTask = $taskRepository->findOneBy(['title' => 'Tâche de test modifiée']);

        // Vérifier que les modifications ont été correctement enregistrées en base de données
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
        $taskForOtherUser = $taskRepository->findOneBy(['User' => 24]);

        $this->assertNotNull($taskForOtherUser);

        $this->accessPage('task_edit', ['id' => $taskForOtherUser->getId()]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }

    public function testEditTaskUnauthenticatedUser(): void
    {
        $this->accessPage('task_edit', ['id' => 63]);

        $this->assertResponseRedirects('/login');

        $crawler = $this->client->followRedirect();

        $this->submitForm($crawler, 'Modifier', [
            'task[title]' => 'Tâche de test modifiée',
            'task[content]' => 'Contenu de la tâche de test modifiée'
        ]);

        $this->assertResponseRedirects('/login');

    }

    /**
     * @throws Exception
     */
    public function testToggleTaskSuccess(): void
    {
        // Se connecter en tant qu'utilisateur "user"
        $this->loginUser('user');

        // Récupérer l'utilisateur connecté
        $user = $this->getLoggedInUser();
        $userId = $user->getId();

        // Récupérer une tâche associée à l'utilisateur connecté
        $taskRepository = $this->getEntityManager()->getRepository(Task::class);
        $taskForCurrentUser = $taskRepository->findOneBy(['User' => $userId]);

        // Vérifier que l'accès à l'URL de basculement de l'état de la tâche renvoie un code 200 (OK)
        $this->accessPage('task_toggle', ['id' => $taskForCurrentUser->getId()]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();

        // Vérifier que la tâche a bien été basculée dans l'état inverse
        $this->assertSelectorTextContains('div.alert.alert-success','La tâche '.$taskForCurrentUser->getTitle().' a bien été marquée comme faite.');

        // Récupérer la tâche mise à jour depuis la base de données
        $updatedTask = $taskRepository->findOneBy(['id' => $taskForCurrentUser->getId()]);

        // Vérifier que l'état de la tâche a bien été mis à jour en base de données
        $this->assertTrue($updatedTask->isDone());
    }

    /**
     * @throws Exception
     */
    public function testToggleTaskUnauthorizedUser(): void
    {
        $this->loginUser('user');

        $taskRepository = $this->getEntityManager()->getRepository(Task::class);
        $taskForOtherUser = $taskRepository->findOneBy(['User' => 24]);

        $this->assertNotNull($taskForOtherUser);

        $this->accessPage('task_toggle', ['id' => $taskForOtherUser->getId()]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }

	public function testToggleTaskUnauthenticatedUser(): void
    {
        $this->accessPage('task_toggle', ['id' => 63]);

        $this->assertResponseRedirects('/login');

    }

    /**
     * @throws Exception
     */
    public function testDeleteTaskByOwner(Crawler $crawler, Users $user): void
    {
        $userIdConnected = $user->getId();
        $task = $this->getEntityManager()
            ->getRepository(Task::class)
            ->findOneBy(['User' => $userIdConnected]);

        $this->assertSame($userIdConnected, $task->getUser()->getId());
        $this->submitForm($crawler, 'Supprimer');

        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche a bien été supprimée.');

    }

    /**
     * @throws Exception
     */
    public function testDeleteTaskByAdmin(Crawler $crawler, Users $adminUser): void
    {
        $taskAnonymous = $this->getEntityManager()
            ->getRepository(Task::class)
            ->findOneBy(['User' => null]);

        $this->assertTrue($taskAnonymous === null && $adminUser->isAdmin());

        $this->submitForm($crawler, 'Supprimer');
        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche a bien été supprimée.');

    }

    /**
     * @throws Exception
     */
    public function testDeleteTaskUnauthorized(Users $user): void
    {
        $userIdConnected = $user->getId();
        $task = $this->getEntityManager()
            ->getRepository(Task::class)
            ->findOneBy(['User' => $userIdConnected]);

        $this->assertNotSame($userIdConnected, $task->getUser()->getId());
        $this->accessPage('task_delete', ['id' => $task->getId()]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }


}