<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerWebTest extends AbstractWebTestCase
{

    /**
     * @throws \Exception
     */
    public function testHomepageAccessForUserSuccess(): void
    {
        $this->loginUser('user');
        $this->accessPage('homepage');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains(
            'h1',
            'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !'
        );
    }

    public function testHomepageAccessForAnonymousForbidden(): void
    {
        $this->accessPage('homepage');
        $this->assertResponseRedirects('http://localhost/login');
    }
}