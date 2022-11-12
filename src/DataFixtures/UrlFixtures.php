<?php

namespace App\DataFixtures;

use App\Entity\Url;
use App\Service\EntityUniqueTokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UrlFixtures extends Fixture
{
    public function __construct(
        private EntityUniqueTokenGenerator $tokenGenerator
    )
    {
    }

    public function load(ObjectManager $manager): void
    {

        $data = [
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://www.youtube.com/watch?v=E6qCnRe3KDw',
            'https://www.youtube.com/watch?v=jPan651rVMs',
            'https://www.youtube.com/watch?v=nzgcmz1aboM'
        ];

        foreach ($data as $urlValue) {
            $url = new Url();

            $url->setLongUrl($urlValue);

            $shortKey = $this->tokenGenerator->generateUniqueToken(4, $manager->getRepository(Url::class));

            $url->setShortKey($shortKey);

            $manager->persist($url);
        }


        $manager->flush();
    }
}
