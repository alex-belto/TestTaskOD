<?php

namespace App\DataFixtures;

use App\Service\User\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    private UserFactory $userFactory;
    public function __construct(UserFactory $userFactory) {
        $this->userFactory = $userFactory;
    }
    public function load(ObjectManager $manager): void
    {
        $admin = $this->userFactory->createAdmin();
        $manager->persist($admin);

        for($i = 0; $i <= 5; $i++) {
            $user = $this->userFactory->createUser();
            $manager->persist($user);
        }
        $manager->flush();
    }
}
