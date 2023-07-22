<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Task;
use App\Tests\Functional\AbstractTestCase;

class TaskControllerTest extends AbstractTestCase
{

    protected string $entityClass = Task::class;

    public function testListActionIsUp()
    {
		$this->accessPage('task_list');
    }

    public function testCreateActionSuccess()
    {
        $crawler = $this->accessPage('task_create');

        $this->submitForm($crawler, 'Ajouter', [
            'task[title]' => 'Tâche de test',
            'task[content]' => 'Contenu de la tâche de test'
        ]);

        $this->assertSelectorTextContains('div.alert.alert-success','La tâche a été bien été ajoutée.');
        $task = $this->repository->findOneBy(['title' => 'Tâche de test']);
        $this->assertSame('Tâche de test', $task->getTitle());

    }

    public function testEditActionSuccess()
    {
        $taskId = $this->repository->findOneBy(['title' => 'Task 1'])->getId();
        $crawler = $this->accessPage('task_edit', ['id' => $taskId]);

        $this->assertSame('Task 1', $crawler->filter('input[name="task[title]"]')->attr('value'));
        $this->assertSame('Content 1', $crawler->filter('textarea[name="task[content]"]')->text());

        $this->submitForm($crawler, 'Modifier', [
            'task[title]' => 'Tâche de test modifiée',
            'task[content]' => 'Contenu de la tâche de test modifiée'
        ]);

        $this->assertSelectorTextContains('div.alert.alert-success','La tâche a bien été modifiée.');
        $task = $this->repository->findOneBy(['title' => 'Tâche de test modifiée']);
        $this->assertSame('Tâche de test modifiée', $task->getTitle());
    }

    public function testToggleTaskActionSuccess()
    {
        $crawler = $this->accessPage('task_list');
        $this->submitForm($crawler, 'Marquer comme faite');

        $this->assertSelectorTextContains('div.alert.alert-success', 'Superbe ! La tâche Task 1 a bien été marquée comme faite.');
        $task = $this->repository->findOneBy(['title' => 'Task 1']);
        $this->assertTrue($task->isDone());
    }

    public function testDeleteTaskAction()
    {
        $crawler = $this->accessPage('task_list');
        $this->submitForm($crawler, 'Supprimer');

        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche a bien été supprimée.');
        $task = $this->repository->findOneBy(['title' => 'Task 1']);
        $this->assertNull($task);

    }
}