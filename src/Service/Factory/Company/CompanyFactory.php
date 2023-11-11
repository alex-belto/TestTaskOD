<?php

namespace App\Service\Factory\Company;

use App\Entity\Company;
use Faker\Factory;

class CompanyFactory
{
    public function createCompany(): Company
    {
        $faker = Factory::create();

        $company = new Company();
        $company->setName($faker->company());

        return $company;
    }
}