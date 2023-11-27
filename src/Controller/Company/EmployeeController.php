<?php

namespace App\Controller\Company;

use App\Entity\Company;
use App\Entity\Employee;
use App\Form\SelectorType\ProjectSelectorType;
use App\Service\Form\FormErrorHandler;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    private EntityManagerInterface $em;
    private FormErrorHandler $formErrorHandler;
    public function __construct(
        EntityManagerInterface $em,
        FormErrorHandler $formErrorHandler
    ){
        $this->em = $em;
        $this->formErrorHandler = $formErrorHandler;
    }

    #[Route('/companies/{id}/employees', name: 'company_employee_index', methods: ['GET'])]
    public function index(Company $company): JsonResponse
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

    #[Route('/companies/{id}/employees/{employee_id}', name: 'company_employee_add', methods: ['PUT'])]
    #[ParamConverter('employee', options: ['id' => 'employee_id'])]
//    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Company $company, Employee $employee): JsonResponse
    {
        $form = $this->createFormBuilder($company, ['csrf_protection' => false])
            ->add('employees', ProjectSelectorType::class)
            ->getForm();

        $form->submit(['employee' => $employee->getId()]);

        if(!$form->isSubmitted() || !$form->isValid()) {
            $errors = $this->formErrorHandler->handleError($form);
            return $this->json($errors , 400);
        }
        $this->em->flush();

        return $this->json(sprintf('Employee %s successfully added to company %s!', $employee->getFullName(), $company->getName()));
    }

    #[Route('/companies/{id}/employees/{employee_id}', name: 'company_employee_remove', methods: ['DELETE'])]
    #[ParamConverter('employee', options: ['id' => 'employee_id'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(Company $company, Employee $employee): JsonResponse
    {
        $company->removeEmployee($employee);
        $this->em->flush();

        return $this->json(sprintf('Employee %s successfully remove from company %s!',$employee->getFullName(), $company->getName()));
    }

}