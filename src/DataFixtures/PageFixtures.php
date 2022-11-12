<?php

namespace App\DataFixtures;

use App\Factory\Page\PageFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PageFixtures extends Fixture
{

    public function __construct(
        private PageFactory $factory,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = [
            $this->factory->createPublishedInMenu('Features', "<l><li>Fast</li><li>Easy</li><li>Simple</li></l> "),
            $this->factory->createPublishedInMenu('Pricing', "Our service is <b>Free</b>"),
            $this->factory->createPublishedInMenu('FAQs', "Hello world!"),
            $this->factory->createPublishedInMenu('About', "This is an example page"),
        ];

        foreach ($data as $page) {
            $manager->persist($page);
        }

        $manager->flush();

    }
}
