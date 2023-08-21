<?php

namespace App\Tests\Functional\Controller\User;

use App\Entity\Users;
use App\Tests\Functional\AbstractWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class EditUserWebTest extends AbstractWebTestCase
{

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
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert.alert-danger','Vous n\'avez pas les droits pour effectuer cette action.');

    }

    public function testEditUserUnauthenticated(): void
    {
        $this->accessPage('user_edit', ['id' => 1]);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertResponseRedirects('http://localhost/login');



    }

}