<?php

namespace App\DataFixtures;

use App\Factory\User\AdminUserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdminFixtures extends Fixture
{
    public function __construct(
        private AdminUserFactory $adminUserFactory,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = $this->adminUserFactory->createDefault();

        $manager->persist($admin);

        $manager->flush();
    }
}
