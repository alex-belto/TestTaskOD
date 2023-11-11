<?php

namespace App\Service\Factory\Project;

use App\Entity\Company;
use App\Entity\Project;
use App\Repository\CompanyRepository;
use Faker\Factory;

class ProjectFactory
{
    public function createProject(Company $company): Project
    {
        $faker = Factory::create();

        $project = new Project();
        $project->setName($faker->domainName() . 'Project');
        $project->setCompany($company);

        return $project;
    }

}