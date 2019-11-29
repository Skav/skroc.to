<?php

namespace App\Tests\Bundle;

use ApiTestCase\Symfony\WebTestCase;
use App\DataFixtures\LinksFixtures;
use App\Repository\LinksRepository;
use App\Services\LinkBundle;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class LinkBundleTest extends WebTestCase
{

    private $linkBundle;

    public function setUp(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $doctrine = $container->get('doctrine');
        $entityManager = $doctrine->getManager();
        $this->linkBundle = new LinkBundle($entityManager);

        $fixture = new LinksFixtures();
        $fixture->load($entityManager);
    }

    public function testGetListOfLinks()
    {
        $data = $this->linkBundle->getListOfLinks();

        $this->assertTrue(is_array($data));
        $this->assertEquals(2, sizeof($data));
        $this->assertEquals(4, sizeof($data[0]));
        $this->assertEquals(4, sizeof($data[1]));
    }

    public function testCheckIsLinkInDataBase()
    {
        $resultTrue = $this->linkBundle->checkIsLinkInDataBase('www.wp.pl');
        $resultFalse = $this->linkBundle->checkIsLinkInDataBase('www.google.pl');

        $this->assertTrue($resultTrue);
        $this->assertFalse($resultFalse);
    }

    public function testCreateNewLink()
    {
        $base = 'www.skroc.to/';
        $slug = 'RghYt';

        $newLink = $this->linkBundle->createNewLink($slug);

        $this->assertEquals($base.$slug, $newLink);
    }

    public function testGetExistedShortLink()
    {
        $originalLink = 'www.wp.pl';

        $result = $this->linkBundle->getExistedShortLink($originalLink);

        $this->assertTrue(is_object($result));
        $this->assertEquals($result->getOriginalLink(), 'www.wp.pl');
        $this->assertEquals($result->getShortLink(), 'www.skroc.to/FFgh4');
        $this->assertEquals($result->getSlug(), 'FFgh4');
    }

    public function testGetElementBySlug()
    {
        $slug = 'Kljk4';

        $result = $this->linkBundle->getElementBySlug($slug);

        $this->assertTrue(is_object($result));
        $this->assertEquals($result->getOriginalLink(), 'www.google.com');
        $this->assertEquals($result->getShortLink(), 'www.skroc.to/Kljk4');
        $this->assertEquals($result->getSlug(), 'Kljk4');
    }

    public function testAddLinkToDatabase()
    {
        $originalLink = 'www.facebook.com';
        $shortLink = 'www.skroc.to/Ghjk5';
        $slug = 'Ghjk5';

        $result = $this->linkBundle->addLinkToDatabase($originalLink, $shortLink, $slug);

        $this->assertTrue(is_object($result));
        $this->assertEquals($result->getOriginalLink(), $originalLink);
        $this->assertEquals($result->getShortLink(), $shortLink);
        $this->assertEquals($result->getSlug(), $slug);
    }

    public function testGetUniqueSlug()
    {
        $slug5 = $this->linkBundle->getUniqueSlug();
        $slug10 = $this->linkBundle->getUniqueSlug(10);

        $this->assertTrue(is_string($slug5));
        $this->assertTrue(is_string($slug10));
        $this->assertEquals(5, strlen($slug5));
        $this->assertEquals(10, strlen($slug10));
    }
}
