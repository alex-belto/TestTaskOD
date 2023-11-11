<?php

namespace App\Controller\Company;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/companies/employees_show/{id}', name: 'company_employee_show', methods: ['GET'])]
    public function showEmployeeAtCompany(Company $company): JsonResponse
    {
        $employeesArray = [];

        foreach ($company->getEmployees() as $employee) {
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

    #[Route('/companies/employees_add/{id}', name: 'company_employee_add', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addEmployeeToCompany(Request $request, Company $company): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $employee = $data['employee'];
        $company->addEmployee($employee);
        $this->em->flush();

        return $this->json('Employee successfully added to company' . $company->getName() . ' !');
    }

    #[Route('/companies/employees_delete/{id}', name: 'company_employee_delete', methods: ['DELETE'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteEmployeeInCompany(Request $request, Company $company): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $employee = $data['employee'];
        $company->removeEmployee($employee);
        $this->em->flush();

        return $this->json('Employee successfully deleted from company' . $company->getName() . ' !', 204);
    }

}