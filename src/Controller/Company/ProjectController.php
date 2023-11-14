<?php

namespace App\Controller\Company;

use App\Entity\Company;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/companies/{id}/projects', name: 'company_project_index', methods: ['GET'])]
    public function index(Company $company): JsonResponse
    {
        $projects = $company->getProjects();
        $projectsArray = [];

        foreach ($projects as $project) {
            $projectsArray[] = [
                'project_name' => $project->getName()
            ];
        }
        return $this->json($projectsArray);
    }

    #[Route('/companies/{id}/projects/{project_id}', name: 'company_project_add', methods: ['PUT'])]
    #[ParamConverter('project', options: ['id' => 'project_id'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Company $company, Project $project): JsonResponse
    {
        $company->addProject($project);
        $this->em->flush();

        return $this->json(sprintf('Project %s successfully added to company %s!', $project->getName(), $company->getName()));
    }

    #[Route('/companies/{id}/projects/{project_id}', name: 'company_project_remove', methods: ['DELETE'])]
    #[ParamConverter('project', options: ['id' => 'project_id'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(Company $company, Project $project): JsonResponse
    {
        $company->removeProject($project);
        $this->em->flush();

        return $this->json(sprintf('Project %s successfully removed from company %s!',$project->getName(), $company->getName()));
    }
}