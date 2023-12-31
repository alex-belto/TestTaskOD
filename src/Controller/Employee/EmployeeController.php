<?php

namespace App\Controller\Employee;

use App\Entity\Employee;
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
    #[Route('/employees', name: 'employee_show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        $employees = $this->employeeRepository->findAll();
        $employeesArray = [];

        foreach ($employees as $employee) {
            $employeesArray = [
                'firstName' => $employee->getFirstName(),
                'lastName' => $employee->getLastName(),
                'salary' => $employee->getSalary(),
                'company' => $employee->getCompany()->getName(),
                'project' => $employee->getProject()->getName(),
            ];
        }

        return $this->json($employeesArray);
    }

    #[Route('/employees/{id}', name: 'employee_get_one', methods: ['GET'])]
    public function getEmployee(Employee $employee): JsonResponse
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
    public function createEmployee(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $employee = $this->employeeFactory->createEmployee(
            $data['company'],
            $data['user'],
            $data['project'],
            $data['firstName'],
            $data['lastName'],
            $data['salary']
        );
        $this->em->persist($employee);
        $this->em->flush();
        return $this->json($employee, 201);
    }

    #[Route('/employees/{id}', name: 'employee_update', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function updateEmployee(Request $request, Employee $employee): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $employee->setCompany($data['company']);
        $employee->setProject($data['project']);
        $employee->setConnectedUser($data['user']);
        $employee->setFirstName($data['firstName']);
        $employee->setLastName($data['lastName']);
        $employee->setSalary($data['salary']);
        $this->em->flush();

        return $this->json($employee);
    }


    #[Route('/employees/{id}', name: 'employee_delete', methods: ['DELETE'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteEmployee(Employee $employee): JsonResponse
    {
        $this->em->remove($employee);
        $this->em->flush();

        return $this->json('Employee successfully deleted!', 204);
    }
}