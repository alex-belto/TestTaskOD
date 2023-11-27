<?php

namespace App\Form\DataTransformer;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UserToNumberTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @param User|null $value
     * @return string
     */
    public function transform($value): string
    {
        if($value === null) {
            return '';
        }

        return $value->getId();
    }

    /**
     * @param string $value
     * @return User|null
     */
    public function reverseTransform($value): ?User
    {
        if(!$value) {
            return null;
        }

        $user = $this->em->getRepository(User::class)->find($value);

        if($user === null) {
            throw new TransformationFailedException(sprintf('A User with id %s does not exist!', $value));
        }

        return $user;
    }
}