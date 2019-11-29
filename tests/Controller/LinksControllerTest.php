<?php

namespace App\Tests;

use ApiTestCase\JsonApiTestCase;
use App\DataFixtures\LinksFixtures;
use Symfony\Component\HttpFoundation\Response;

class LinksControllerTest extends JsonApiTestCase
{

    public function setUp(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $doctrine = $container->get('doctrine');
        $entityManager = $doctrine->getManager();

        $fixture = new LinksFixtures();
        $fixture->load($entityManager);
    }

    public function testAddNewShort()
    {
        $data = ['original_link' => "www.google.pl"];

        $this->client->request('POST', 'api/links', $data);

        $request = $this->client->getResponse();

        $this->assertResponse($request, 'create_link', Response::HTTP_CREATED);
    }

    public function testAddNewShortWhenExist()
    {
        $data = ['original_link' => "www.wp.pl"];

        $this->client->request('POST', 'api/links', $data);

        $request = $this->client->getResponse();

        $this->assertResponse($request, 'create_link_error', Response::HTTP_ALREADY_REPORTED);
    }

    public function testGetElementBySlug()
    {
        $this->client->request('GET', 'api/links/FFgh4');

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'get_link', Response::HTTP_OK);
    }

    public function testGetListOfLinks()
    {
        $this->client->request('GET', 'api/links');

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'list_of_links');
    }
}
