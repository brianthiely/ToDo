<?php

namespace App\Tests\Functional\Controller\User;

use App\Tests\Functional\AbstractWebTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ListUserWebTest extends AbstractWebTestCase
{
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


}