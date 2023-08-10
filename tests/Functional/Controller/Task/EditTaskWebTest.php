<?php

namespace App\Tests\Functional\Controller\Task;

use App\Entity\Task;
use App\Tests\Functional\AbstractWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class EditTaskWebTest extends AbstractWebTestCase
{

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

            $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        } else {
            $this->assertTrue(true);
        }
    }


    public function testEditTaskUnauthenticatedUser(): void
    {
        $this->accessPage('task_edit', ['id' => 63]);
        $this->assertResponseRedirects('http://localhost/login');

    }

}