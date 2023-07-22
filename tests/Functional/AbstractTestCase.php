<?php

namespace App\Tests\Functional;

use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;



abstract class AbstractTestCase extends WebTestCase
{
    protected ?KernelBrowser $client = null;

    protected RouterInterface $urlGenerator;

    protected ObjectRepository $repository;

    protected string $entityClass;


    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {

        $this->client = static::createClient();

        $this->repository = self::getContainer()->get('doctrine')->getRepository($this->entityClass);

        $this->urlGenerator = self::getContainer()->get(RouterInterface::class);

    }


    protected function accessPage(string $routeName, array $routeParams = [], string $method = 'GET'): Crawler
    {
        $url = $this->urlGenerator->generate($routeName, $routeParams);
        $this->client->request($method, $url);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        return $this->client->getCrawler();
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