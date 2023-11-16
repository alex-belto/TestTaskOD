<?php

namespace App\Controller\Employee;

use App\Entity\Company;
use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\EmployeeRepository;
use App\Service\Factory\Employee\EmployeeFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    private EmployeeRepository $employeeRepository;
    private EmployeeFactory $employeeFactory;
    private EntityManagerInterface $em;
    public function __construct(
        EmployeeRepository $employeeRepository,
        EmployeeFactory $employeeFactory,
        EntityManagerInterface $em
    ) {
        $this->employeeRepository = $employeeRepository;
        $this->employeeFactory = $employeeFactory;
        $this->em = $em;
    }
    #[Route('/employees', name: 'employee_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $employees = $this->employeeRepository->findAll();
        $employeesArray = [];

        foreach ($employees as $employee) {
            $employeesArray[] = [
                'firstName' => $employee->getFirstName(),
                'lastName' => $employee->getLastName(),
                'salary' => $employee->getSalary(),
                'company' => $employee->getCompany()->getName(),
                'project' => $employee->getProject()->getName(),
            ];
        }

        return $this->json($employeesArray);
    }

    #[Route('/employees/{id}', name: 'employee_show', methods: ['GET'])]
    public function show(Employee $employee): JsonResponse
    {
        $employee = [
            'firstName' => $employee->getFirstName(),
            'lastName' => $employee->getLastName(),
            'salary' => $employee->getSalary(),
            'company' => $employee->getCompany()->getName(),
            'project' => $employee->getProject()->getName(),
        ];
        return $this->json($employee);
    }

    #[Route('/employees', name: 'employee_create', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $company = $this->em->getRepository(Company::class)->find($data['company_id']);
        $project = $this->em->getRepository(Project::class)->find($data['project_id']);
        $user = $this->em->getRepository(User::class)->find($data['user_id']);

        $employee = $this->employeeFactory->createEmployee(
            $company,
            $user,
            $project,
            $data['firstName'],
            $data['lastName'],
            $data['salary']
        );
        $this->em->persist($employee);
        $this->em->flush();
        return $this->json(sprintf('Employee %s successfully created!', $employee->getFullName()), 201);
    }

    #[Route('/employees/{id}', name: 'employee_update', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function update(Request $request, Employee $employee): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $company = $this->em->getRepository(Company::class)->find($data['company_id']);
        $project = $this->em->getRepository(Project::class)->find($data['project_id']);
        $user = $this->em->getRepository(User::class)->find($data['user_id']);
        $employee->setCompany($company);
        $employee->setProject($project);
        $employee->setConnectedUser($user);
        $employee->setFirstName($data['firstName']);
        $employee->setLastName($data['lastName']);
        $employee->setSalary($data['salary']);
        $this->em->flush();

        return $this->json(sprintf('Employee %s successfully updated!', $employee->getFullName()));
    }

    #[Route('/employees/{id}', name: 'employee_delete', methods: ['DELETE'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Employee $employee): JsonResponse
    {
        $this->em->remove($employee);
        $this->em->flush();

        return $this->json(sprintf('Employee %s successfully deleted!', $employee->getFullName()));
    }
}