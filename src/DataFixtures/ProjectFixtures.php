<?php

namespace App\DataFixtures;

use App\Repository\CompanyRepository;
use App\Service\Factory\Project\ProjectFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    private ProjectFactory $projectFactory;
    private CompanyRepository $companyRepository;
    public function __construct(
        ProjectFactory $projectFactory,
        CompanyRepository $companyRepository
    ) {
        $this->projectFactory = $projectFactory;
        $this->companyRepository = $companyRepository;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $companies = $this->companyRepository->findAll();
        $idCounter = 1;

        foreach ($companies as $company) {
            $project = $this->projectFactory->createProject($company, $faker->domainName());
            $manager->persist($project);
            $referenceName = sprintf('projectReference%s', $idCounter);
            $this->addReference($referenceName, $project);
            $idCounter++;
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [CompanyFixtures::class];
    }
}