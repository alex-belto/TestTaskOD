<?php

namespace App\Controller\Project;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    #[Route('/projects/employees_show/{id}', name: 'project_employee_show', methods: ['GET'])]
    public function showEmployeeAtProject(Project $project): JsonResponse
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

    #[Route('/projects/employees_add/{id}', name: 'project_employee_add', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addEmployeeToProject(Request $request, Project $project): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $employee = $data['employee'];
        $project->addEmployee($employee);
        $this->em->flush();

        return $this->json('Employee successfully added to project' . $project->getName() . ' !');
    }

    #[Route('/project/employees_delete/{id}', name: 'project_employee_delete', methods: ['DELETE'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteEmployeeInProject(Request $request, Project $project): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $employee = $data['employee'];
        $project->removeEmployee($employee);
        $this->em->flush();

        return $this->json('Employee successfully deleted from project' . $project->getName() . ' !', 204);
    }

}