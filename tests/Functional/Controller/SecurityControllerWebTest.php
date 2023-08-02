<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerWebTest extends AbstractWebTestCase
{

    public function testLoginSuccess() : void
    {
        $crawler = $this->accessPage('app_login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);


        $this->submitForm($crawler, 'Sign in', [
            '_username' => 'user',
            '_password' => 'user'
        ]);

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');
        $token = $tokenStorage->getToken();

        $this->assertNotNull($token);
        $this->assertSame('user', $token->getUser()->getUsername());

    }

    public function testLoginFailure() : void
    {
        $crawler = $this->accessPage('app_login');

        $this->submitForm($crawler, 'Sign in', []);

        $this->assertSelectorTextContains('div.alert.alert-danger', 'Invalid credentials');
    }

    public function testLogout() : void
    {
        $this->accessPage('app_logout');

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');
        $token = $tokenStorage->getToken();

        $this->assertNull($token);
    }

    public function testRememberMeFunctionality() : void
    {
        $crawler = $this->accessPage('app_login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->submitForm($crawler, 'Sign in', [
            '_username' => 'user',
            '_password' => 'user',
            '_remember_me' => true,
        ]);

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');
        $token = $tokenStorage->getToken();
        $this->assertNotNull($token);
        $this->assertSame('user', $token->getUser()->getUsername());

        // Fermer le navigateur ou déconnecter l'utilisateur (simulateur de déconnexion)
        $this->client->restart();

        $token = $tokenStorage->getToken();
        $this->assertNotNull($token);
        $this->assertSame('user', $token->getUser()->getUsername());
    }

}
