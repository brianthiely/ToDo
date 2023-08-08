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

    /**
     * @throws Exception
     */
    public function testListUserSuccessForAdmin()
    {
        $this->loginUser('admin');
        $this->accessPage('user_list');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @throws Exception
     */
    public function testListUserForbiddenForUser()
    {
        $this->loginUser('user');
        $this->accessPage('user_list');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @throws Exception
     */
    public function testEditUserSuccessByAdmin(): void
    {
        $this->loginUser('admin');

        $userRepository = $this->getEntityManager()->getRepository(Users::class);
        $userToEdit = $userRepository->findOneBy(['username' => 'user']);
        $this->assertNotNull($userToEdit, 'Aucun utilisateur avec le roles user trouvé');

        $userIdToEdit = $userToEdit->getId();

        $crawler = $this->accessPage('user_edit', ['id' => $userIdToEdit]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->submitForm($crawler, 'Modifier', [
            'user[username]' => 'UserEdit',
            'user[password][first]' => 'PasswordEdit',
            'user[password][second]' => 'PasswordEdit',
            'user[email]' => 'emailEdit@mail.com',
            'user[roles]' => 'ROLE_ADMIN'
        ]);

        $this->assertSelectorTextContains('div.alert.alert-success','L\'utilisateur a bien été modifié');

        $updatedUser = $userRepository->findOneBy(['username' => 'UserEdit']);
        $this->assertNotNull($updatedUser, 'Aucun utilisateur avec le username UserEdit trouvé');
        $this->assertNotSame($userToEdit, $updatedUser);

    }

    /**
     * @throws Exception
     */
    public function testEditUserUnauthorizedByUser(): void
    {
        $this->loginUser('user');

        $user = $this->getLoggedInUser();
        $userId = $user->getId();

        $this->accessPage('user_edit', ['id' => $userId]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }

    public function testEditUserUnauthenticated(): void
    {
        $this->accessPage('user_edit', ['id' => 1]);

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $redirectUrl = $this->client->getResponse()->headers->get('location');
        $this->assertEquals('/login', parse_url($redirectUrl, PHP_URL_PATH));


    }

}