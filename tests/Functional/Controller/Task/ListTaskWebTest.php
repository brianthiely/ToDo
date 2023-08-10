<?php


namespace App\Tests\Functional\Controller\Task;

use App\Entity\Task;
use App\Tests\Functional\AbstractWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ListTaskWebTest extends AbstractWebTestCase
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
        $this->assertResponseRedirects('http://localhost/login');

    }


}