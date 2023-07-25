<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\AbstractTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends AbstractTestCase
{

    public function testHomepageIsUp(): void
    {
        $this->accessPage('homepage');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !');
    }
}