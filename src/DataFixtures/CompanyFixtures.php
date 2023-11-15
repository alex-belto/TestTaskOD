<?php

namespace App\DataFixtures;

use App\Service\Factory\Company\CompanyFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CompanyFixtures extends Fixture
{
    private CompanyFactory $companyFactory;
    public function __construct(CompanyFactory $companyFactory) {
        $this->companyFactory = $companyFactory;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for($i = 1; $i <= 5; $i++) {
            $company = $this->companyFactory->createCompany($faker->company());
            $manager->persist($company);
            $referenceName = sprintf('companyReference%s', $i);
            $this->addReference($referenceName, $company);
        }
        $manager->flush();
    }
}