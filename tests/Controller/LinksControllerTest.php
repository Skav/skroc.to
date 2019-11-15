<?php

namespace App\Tests;

use ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\LinksController;

class LinksControllerTest extends JsonApiTestCase
{

    public function testAddNewShort()
    {
        $data = ['original_link' => "www.wp.pl"];

        $this->client->request('POST', 'api/links', $data);

        $request = $this->client->getResponse();

        $this->assertResponse($request, 'create_link', Response::HTTP_CREATED);
    }

    public function testAddNewShortWhenExist()
    {
        $data = ['original_link' => "www.wp.pl"];

        $this->client->request('POST', 'api/links', $data);

        $request = $this->client->getResponse();

        $this->assertResponse($request, 'create_link', Response::HTTP_ALREADY_REPORTED);
    }

    public function testGetElementBySlug()
    {
        //$object = $this->loadFixturesFromDirectory();
        $this->client->request('GET', 'api/links', ['slug' => 'zIv5Cb']);

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
