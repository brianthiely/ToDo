<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;



abstract class AbstractWebTestCase extends WebTestCase
{
    protected ?KernelBrowser $client = null;

    protected RouterInterface $urlGenerator;




    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->urlGenerator = self::getContainer()->get(RouterInterface::class);
    }

    /**
     * @throws Exception
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function accessPage(string $routeName, array $routeParams = [], string $method = 'GET'): Crawler
    {
        $url = $this->urlGenerator->generate($routeName, $routeParams);
        $this->client->request($method, $url);


        return $this->client->getCrawler();
    }


    /**
     * @throws Exception
     */
    protected function loginUser(string $username): void
    {
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['username' => $username]);
        $this->client->loginUser($user);
    }

    /**
     * @throws Exception
     */
    protected function getLoggedInUser(): User
    {
        $tokenStorage  = self::getContainer()->get('security.token_storage');
        return $tokenStorage->getToken()->getUser();
    }

    protected function submitForm(Crawler $crawler, string $buttonText, array $formData = []): Crawler
    {
        $form = $crawler->selectButton($buttonText)->form();

        foreach ($formData as $field => $value) {
            $form[$field] = $value;
        }

        $crawler = $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        return $crawler;
    }

}