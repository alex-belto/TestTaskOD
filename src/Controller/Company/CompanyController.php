<?php

namespace App\Controller\Company;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Service\Factory\Company\CompanyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    #[Route('/companies', name: 'company_show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        $companies = $this->companyRepository->findAll();
        return $this->json($companies);
    }

    #[Route('/companies/{id}', name: 'company_get_one', methods: ['GET'])]
    public function getCompany(Company $company): JsonResponse
    {
        return $this->json($company);
    }

    #[Route('/companies', name: 'company_create', methods: ['POST'])]
    public function createCompany(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $company = $this->companyFactory->createCompany($data['name']);
        $this->em->persist($company);
        $this->em->flush();
        return $this->json($company, 201);
    }

    #[Route('/companies/{id}', name: 'company_update', methods: ['PUT'])]
    public function updateCompany(Request $request, Company $company): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $company->setName($data['name']);
        $this->em->flush();

        return $this->json($company);
    }

    #[Route('/companies/{id}', name: 'company_delete', methods: ['DELETE'])]
    public function deleteCompany(Company $company): JsonResponse
    {
        $this->em->remove($company);
        $this->em->flush();

        return $this->json('Company successfully deleted!', 204);
    }
}