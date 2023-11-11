<?php

namespace App\Service\Factory\User;

use App\Entity\User;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher) {
        $this->hasher = $hasher;
    }
    public function createAdmin(): User
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $password = $this->hasher->hashPassword($user, 'admin');
        $user->setPassword($password);

        return $user;
    }

    public function createRandomUser(): User
    {
        $faker = Factory::create();
        $user = new User();
        $user->setUsername($faker->userName());
        $user->setRoles(['ROLE_USER']);
        $password = $this->hasher->hashPassword($user, 'user');
        $user->setPassword($password);

        return $user;
    }

    public function createUser(string $username, string $password): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setRoles(['ROLE_USER']);
        $password = $this->hasher->hashPassword($user, $password);
        $user->setPassword($password);

        return $user;
    }

}