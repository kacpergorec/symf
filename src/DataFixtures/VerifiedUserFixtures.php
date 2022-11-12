<?php

namespace App\DataFixtures;

use App\Factory\User\VerifiedUserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VerifiedUserFixtures extends Fixture
{

    public function __construct(
        private VerifiedUserFactory $userFactory,
    )
    {
    }


    public function load(ObjectManager $manager): void
    {
        $verifiedUser = $this->userFactory->createNew(
            'user',
            'user@email.com',
            '123'
        );

        $manager->persist($verifiedUser);

        $manager->flush();
    }
}
