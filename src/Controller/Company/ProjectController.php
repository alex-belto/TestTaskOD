<?php

namespace App\Controller\Company;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/companies/project_show/{id}', name: 'company_project_show', methods: ['GET'])]
    public function showProjectsAtCompany(Company $company): JsonResponse
    {
        $projects = $company->getProjects();
        return $this->json($projects);
    }

    #[Route('/companies/project_add/{id}', name: 'company_project_add', methods: ['PUT'])]
    public function addProjectToCompany(Request $request, Company $company): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $project = $data['project'];
        $company->addProject($project);
        $this->em->flush();

        return $this->json('Project successfully added to company' . $company->getName() . ' !');
    }

    #[Route('/companies/project_delete/{id}', name: 'company_project_delete', methods: ['DELETE'])]
    public function deleteProjectInCompany(Request $request, Company $company): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $project = $data['project'];
        $company->removeProject($project);
        $this->em->flush();

        return $this->json('Project successfully deleted from company' . $company->getName() . ' !', 204);
    }
}