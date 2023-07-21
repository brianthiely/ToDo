<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;


    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->taskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);


        $this->urlGenerator = $this->client->getContainer()->get('router.default');

    }


    public function testListActionIsUp()
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}