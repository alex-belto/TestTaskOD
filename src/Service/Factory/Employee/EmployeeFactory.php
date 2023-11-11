<?php

namespace App\Service\Factory\Employee;

use App\Entity\Company;
use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\User;
use Faker\Factory;

class EmployeeFactory
{
    public function createEmployee(
        Company $company,
        User $user,
        Project $project,
        string $firstName,
        string $lastName,
        int $salary
    ): Employee
    {
        $employee = new Employee();
        $employee->setCompany($company);
        $employee->setProject($project);
        $employee->setConnectedUser($user);
        $employee->setFirstName($firstName);
        $employee->setLastName($lastName);
        $employee->setSalary($salary);

        return $employee;
    }
    public function createRandomEmployee(Company $company, User $user, Project $project): Employee
    {
        $faker = Factory::create();

        $employee = new Employee();
        $employee->setCompany($company);
        $employee->setProject($project);
        $employee->setConnectedUser($user);
        $employee->setFirstName($faker->firstName());
        $employee->setLastName($faker->lastName());
        $employee->setSalary($faker->numberBetween(500, 7000));

        return $employee;
    }
}