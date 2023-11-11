<?php

namespace App\Controller\User;

use App\Entity\Company;
use App\Entity\User;
use App\Service\Factory\User\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserFactory $userFactory;
    public function __construct(
        EntityManagerInterface $em,
        UserFactory $userFactory
    ) {
        $this->em = $em;
        $this->userFactory = $userFactory;
    }

    #[Route('/users', name: 'user_create', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function createUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->userFactory->createUser($data['username'], $data['password']);
        $this->em->persist($user);
        $this->em->flush();
        return $this->json('User successfully created!', 201);
    }

    #[Route('/users/{id}', name: 'user_delete', methods: ['DELETE'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteUser(User $user): JsonResponse
    {
        $this->em->remove($user);
        $this->em->flush();

        return $this->json('User successfully deleted!', 204);
    }
}