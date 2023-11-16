<?php

namespace App\Controller\Project;

use App\Entity\Company;
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
    #[Route('/projects', name: 'project_index', methods: ['GET'])]
    public function index(): JsonResponse
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

    #[Route('/projects/{id}', name: 'project_show', methods: ['GET'])]
    public function show(Project $project): JsonResponse
    {
        $projectsArray = [
            'project_name' => $project->getName(),
            'company_name' => $project->getCompany()->getName()
        ];
        return $this->json($projectsArray);
    }

    #[Route('/projects', name: 'project_create', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $company = $this->em->getRepository(Company::class)->find($data['company_id']);

        $project = $this->projectFactory->createProject($company, $data['name']);
        $this->em->persist($project);
        $this->em->flush();
        return $this->json(sprintf('Project %s successfully created!', $project->getName()), 201);
    }

    #[Route('/projects/{id}', name: 'project_update', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function update(Request $request, Project $project): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $company = $this->em->getRepository(Company::class)->find($data['company_id']);

        $project->setName($data['name']);
        $project->setCompany($company);
        $this->em->flush();

        return $this->json(sprintf('Project %s successfully updated!', $project->getName()));
    }

    #[Route('/projects/{id}', name: 'project_delete', methods: ['DELETE'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Project $project): JsonResponse
    {
        $this->em->remove($project);
        $this->em->flush();

        return $this->json(sprintf('Project %s successfully deleted!', $project->getName()));
    }
}