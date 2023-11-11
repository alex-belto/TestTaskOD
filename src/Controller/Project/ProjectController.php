<?php

namespace App\Controller\Project;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Service\Factory\Project\ProjectFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    private ProjectRepository $projectRepository;
    private ProjectFactory $projectFactory;
    private EntityManagerInterface $em;
    public function __construct(
        ProjectRepository $projectRepository,
        ProjectFactory $projectFactory,
        EntityManagerInterface $em
    ) {
        $this->projectRepository = $projectRepository;
        $this->projectFactory = $projectFactory;
        $this->em = $em;
    }
    #[Route('/projects', name: 'project_show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        $projects = $this->projectRepository->findAll();
        $projectsArray = [];

        foreach ($projects as $project) {
            $projectsArray[] = [
                'project_name' => $project->getName(),
                'company_name' => $project->getCompany()->getName()
            ];
        }
        return $this->json($projectsArray);
    }

    #[Route('/projects/{id}', name: 'project_get_one', methods: ['GET'])]
    public function getProject(Project $project): JsonResponse
    {
        $projectsArray = [
            'project_name' => $project->getName(),
            'company_name' => $project->getCompany()->getName()
        ];
        return $this->json($projectsArray);
    }

    #[Route('/projects', name: 'project_create', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function createProject(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $project = $this->projectFactory->createProject($data['company'], $data['name']);
        $this->em->persist($project);
        $this->em->flush();
        return $this->json($project, 201);
    }

    #[Route('/projects/{id}', name: 'project_update', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function updateProject(Request $request, Project $project): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $project->setName($data['name']);
        $project->setCompany($data['company']);
        $this->em->flush();

        return $this->json($project);
    }

    #[Route('/projects/{id}', name: 'project_delete', methods: ['DELETE'])]
    public function deleteProject(Project $project): JsonResponse
    {
        $this->em->remove($project);
        $this->em->flush();

        return $this->json('Project successfully deleted!', 204);
    }
}