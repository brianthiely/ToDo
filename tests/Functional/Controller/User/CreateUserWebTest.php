<?php

namespace App\Tests\Functional\Controller\User;

use App\Entity\Users;
use App\Tests\Functional\AbstractWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateUserWebTest extends AbstractWebTestCase
{

    /**
     * @throws Exception
     */
    public function testCreateUserByAdminSuccess()
    {
        $this->loginUser('admin');

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

    /**
     * @throws Exception
     */
    public function testErrorCreateUserWithoutUsernameByAdmin()
    {
        $this->loginUser('admin');

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

    public function testErrorCreateUserWithoutEmailByAdmin()
    {
        $this->loginUser('admin');

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


}