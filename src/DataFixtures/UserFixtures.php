<?php

namespace App\DataFixtures;

use App\Service\Factory\User\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    private UserFactory $userFactory;
    public function __construct(UserFactory $userFactory) {
        $this->userFactory = $userFactory;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $admin = $this->userFactory->createAdmin();
        $manager->persist($admin);

        for($i = 0; $i < 4; $i++) {
            $user = $this->userFactory->createUser($faker->userName(), 'user');
            $manager->persist($user);
        }
        $manager->flush();
    }
}