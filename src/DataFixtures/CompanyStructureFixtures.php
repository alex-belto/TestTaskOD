<?php

namespace App\DataFixtures;

use App\Repository\CompanyRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Service\Factory\Company\CompanyFactory;
use App\Service\Factory\Employee\EmployeeFactory;
use App\Service\Factory\Project\ProjectFactory;
use App\Service\Factory\User\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyStructureFixtures extends Fixture
{
    private UserRepository $userRepository;
    private CompanyRepository $companyRepository;
    private ProjectRepository $projectRepository;
    private EmployeeFactory $employeeFactory;
    private CompanyFactory $companyFactory;
    private ProjectFactory $projectFactory;
    private UserFactory $userFactory;
    public function __construct(
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        ProjectRepository $projectRepository,
        EmployeeFactory $employeeFactory,
        CompanyFactory $companyFactory,
        ProjectFactory $projectFactory,
        UserFactory $userFactory
    ) {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->projectRepository = $projectRepository;
        $this->employeeFactory = $employeeFactory;
        $this->companyFactory = $companyFactory;
        $this->projectFactory = $projectFactory;
        $this->userFactory = $userFactory;
    }


    public function load(ObjectManager $manager)
    {
        $admin = $this->userFactory->createAdmin();
        $manager->persist($admin);

        for($i = 0; $i <= 5; $i++) {
            $user = $this->userFactory->createUser();
            $manager->persist($user);
        }
        $manager->flush();

        for($i = 0; $i <= 5; $i++) {
            $company = $this->companyFactory->createCompany();
            $manager->persist($company);
        }
        $manager->flush();

        for($i = 1; $i < 5; $i++) {
            $companyId = rand(1, count($this->companyRepository->findAll()));
            $company = $this->companyRepository->find($companyId);
            $project = $this->projectFactory->createProject($company);
            $manager->persist($project);
        }
        $manager->flush();

        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $companyId = rand(1, count($this->companyRepository->findAll()));
            $company = $this->companyRepository->find($companyId);
            $projectId = rand(1, count($this->projectRepository->findAll()));
            $project = $this->projectRepository->find($projectId);

                $employee = $this->employeeFactory->createEmployee($company, $user, $project);
                $manager->persist($employee);

        }

        $manager->flush();
    }
}