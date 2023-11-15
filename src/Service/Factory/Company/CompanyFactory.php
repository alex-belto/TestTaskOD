<?php

namespace App\Service\Factory\Company;

use App\Entity\Company;
use Faker\Factory;

class CompanyFactory
{
    public function createCompany(string $name): Company
    {
        $company = new Company();
        $company->setName($name);

        return $company;
    }
}