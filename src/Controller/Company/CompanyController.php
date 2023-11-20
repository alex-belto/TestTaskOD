<?php

namespace App\Controller\Company;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Service\Factory\Company\CompanyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    private CompanyRepository $companyRepository;
    private CompanyFactory $companyFactory;
    private EntityManagerInterface $em;
    public function __construct(
        CompanyRepository $companyRepository,
        CompanyFactory $companyFactory,
        EntityManagerInterface $em
    ) {
        $this->companyRepository = $companyRepository;
        $this->companyFactory = $companyFactory;
        $this->em = $em;
    }
    #[Route('/companies', name: 'company_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $companies = $this->companyRepository->findAll();
        $companiesArray = [];

        foreach ($companies as $company) {

            $projects = [];
            $employees = [];

            foreach ($company->getProjects() as $project) {
                $projects = [
                    'project_name' => $project->getName(),
                ];
            }
            foreach ($company->getEmployees() as $employee) {
                $employees = [
                    'project_employee_name' => $employee->getFirstName() . ' ' . $employee->getLastName(),
                    'project_employee_salary' => $employee->getSalary(),
                ];
            }
            $companiesArray[] = [
                'name' => $company->getName(),
                'projects' => $projects,
                'employee' => $employees
            ];
        }
        return $this->json($companiesArray);
    }

    #[Route('/companies/{id}', name: 'company_show', methods: ['GET'])]
    public function show(Company $company): JsonResponse
    {
        $projects = [];
        $employees = [];

        foreach ($company->getProjects() as $project) {
            $projects = [
                'project_name' => $project->getName(),
            ];
        }
        foreach ($company->getEmployees() as $employee) {
            $employees = [
                'project_employee_name' => $employee->getFirstName() . ' ' . $employee->getLastName(),
                'project_employee_salary' => $employee->getSalary(),
            ];
        }
        $companyArray = [
            'name' => $company->getName(),
            'projects' => $projects,
            'employee' => $employees
        ];

        return $this->json($companyArray);
    }

    #[Route('/companies', name: 'company_create', methods: ['POST'])]
//    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $company = new Company();
        $form = $this->createFormBuilder($company)
            ->add('name', TextType::class)
            ->getForm();

        $form->submit(
            ['form' => [
                "name" => $data['name']
                ]
            ]
        );

        if(!$form->isSubmitted() || !$form->isValid()) {
            $formName = $form->getName();
            $errors = $form->getErrors();
            return $this->json($form->getErrors() , 400);
        }

        $this->em->persist($company);
        $this->em->flush();
        return $this->json(sprintf('Company %s successfully created!', $company->getName()), 201);
    }

    #[Route('/companies/{id}', name: 'company_update', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function update(Request $request, Company $company): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $company->setName($data['name']);
        $this->em->flush();

        return $this->json(sprintf('Company %s successfully updated!', $company->getName()));
    }

    #[Route('/companies/{id}', name: 'company_delete', methods: ['DELETE'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Company $company): JsonResponse
    {
        $companyName = $company->getName();
        $this->em->remove($company);
        $this->em->flush();

        return $this->json(sprintf('Company %s successfully deleted!', $companyName));
    }
}