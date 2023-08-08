<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Users;
use App\Tests\Functional\AbstractWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserControllerWebTest extends AbstractWebTestCase
{

    /**
     * @throws Exception
     */
    public function testCreateUserSuccess()
    {
        $crawler = $this->accessPage('user_create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->submitForm($crawler, 'Ajouter', [
            'user[username]' => 'UserTest',
            'user[password][first]' => 'PasswordTest',
            'user[password][second]' => 'PasswordTest',
            'user[email]' => 'user@test.com',
            'user[roles]' => 'ROLE_USER'
        ]);

        $this->assertSelectorTextContains('div.alert.alert-success','L\'utilisateur a bien été créé');
        $user = $this->getEntityManager()
            ->getRepository(Users::class)
            ->findOneBy(['username' => 'UserTest']);

        $this->assertNotNull($user);

    }

    public function testErrorCreateUserWithoutUsername()
    {
        $crawler = $this->accessPage('user_create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->submitForm($crawler, 'Ajouter', [
            'user[password][first]' => 'PasswordTest',
            'user[password][second]' => 'PasswordTest',
            'user[email]' => 'user@test.com',
            'user[roles]' => 'ROLE_USER'
        ]);

        $this->assertSelectorNotExists('div.alert.alert-success','L\'utilisateur a bien été créé');
    }

    public function testErrorCreateUserWithoutEmail()
    {
        $crawler = $this->accessPage('user_create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->submitForm($crawler, 'Ajouter', [
            'user[username]' => 'UserTest',
            'user[password][first]' => 'PasswordTest',
            'user[password][second]' => 'PasswordTest',
            'user[roles]' => 'ROLE_USER'
        ]);

        $this->assertSelectorNotExists('div.alert.alert-success','L\'utilisateur a bien été créé');
    }

    /**
     * @throws Exception
     */
    public function testListUserSuccess()
    {
        $this->loginUser('admin');
        $this->assertContains('ROLE_ADMIN', $this->getLoggedInUser()->getRoles());
        $this->accessPage('user_list');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @throws Exception
     */
    public function testEditUserSuccess(): void
    {
        $this->loginUser('user');

        $user = $this->getLoggedInUser();
		$userId = $user->getId();

        $crawler = $this->accessPage('user_edit', ['id' => $userId]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->submitForm($crawler, 'Modifier', [
            'user[username]' => 'UserEdit',
            'user[password][first]' => 'PasswordEdit',
            'user[password][second]' => 'PasswordEdit',
            'user[email]' => 'emailEdit@mail.com',
            'user[roles]' => 'ROLE_ADMIN'
        ]);

        $this->assertSelectorTextContains('div.alert.alert-success','L\'utilisateur a bien été modifié');

        $updatedUser = $this->getEntityManager()
            ->getRepository(Users::class)
            ->findOneBy(['username' => 'UserEdit']);

        $this->assertNotSame($user, $updatedUser);

    }

    /**
     * @throws Exception
     */
    public function testEditUserUnauthorized(): void
    {
        $this->loginUser('user');

        $user = $this->getLoggedInUser();
        $userId = $user->getId();

        $this->accessPage('users_edit', ['id' => $userId + 1]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }

    public function testEditUserUnauthenticated(): void
    {
        $this->accessPage('users_edit', ['id' => 1]);

        $this->assertResponseRedirects('/login');
        $crawler = $this->client->followRedirect();

        $this->submitForm($crawler, 'Modifier', [
            'user[username]' => 'UserEdit',
            'user[password][first]' => 'PasswordEdit',
            'user[password][second]' => 'PasswordEdit',
            'user[email]' => 'mailEdit@mail.com',
            'user[roles]' => 'ROLE_ADMIN'
        ]);

        $this->assertResponseRedirects('/login');

    }

}