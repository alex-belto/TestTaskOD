<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Project;
use App\Entity\User;
use App\Service\Factory\Employee\EmployeeFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EmployeeFixtures extends Fixture implements DependentFixtureInterface
{
    private EmployeeFactory $employeeFactory;
    public function __construct(EmployeeFactory $employeeFactory) {
        $this->employeeFactory = $employeeFactory;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $users = $manager->getRepository(User::class)->findAll();
        $idCounter = 1;

        foreach ($users as $user) {
            $company = $this->getReference(sprintf('companyReference%s', $idCounter), Company::class);
            $project = $this->getReference(sprintf('projectReference%s', $idCounter), Project::class);

            $employee = $this->employeeFactory->createEmployee(
                $company,
                $user,
                $project,
                $faker->firstName(),
                $faker->lastName(),
                rand(500, 7000)
            );
            $manager->persist($employee);
            $idCounter++;
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ProjectFixtures::class,
            CompanyFixtures::class
        ];
    }
}