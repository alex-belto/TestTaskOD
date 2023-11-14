<?php

namespace App\Controller\Project;

use App\Entity\Employee;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    #[Route('/projects/{id}/employees', name: 'project_employee_index', methods: ['GET'])]
    public function index(Project $project): JsonResponse
    {
        $employees = $project->getEmployees();
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

    #[Route('/projects/{id}/employees/{employee_id}', name: 'project_employee_add', methods: ['PUT'])]
    #[ParamConverter('employee', options: ['id' => 'employee_id'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Project $project, Employee $employee): JsonResponse
    {
        $project->addEmployee($employee);
        $this->em->flush();

        return $this->json(sprintf('Employee %s successfully added to project %s!', $employee->getFullName(), $project->getName()));
    }

    #[Route('/projects/{id}/employees/{employee_id}', name: 'project_employee_remove', methods: ['DELETE'])]
    #[ParamConverter('employee', options: ['id' => 'employee_id'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(Project $project, Employee $employee): JsonResponse
    {
        $project->removeEmployee($employee);
        $this->em->flush();

        return $this->json(sprintf('Employee %s successfully removed from project %s!', $employee->getFullName(), $project->getName()));
    }

}