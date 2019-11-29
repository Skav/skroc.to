<?php

namespace App\DataFixtures;

use App\Entity\Links;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LinksFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $link = new Links();
        $link->setOriginalLink('www.wp.pl');
        $link->setSlug('FFgh4');
        $link->setShortLink('www.skroc.to/FFgh4');

        $link->setOriginalLink('www.google.com');
        $link->setSlug('Kljk4');
        $link->setShortLink('www.skroc.to/Kljk4');

        $manager->persist($link);
        $manager->flush();
    }
}
