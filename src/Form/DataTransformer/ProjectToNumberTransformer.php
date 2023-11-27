<?php

namespace App\Form\DataTransformer;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ProjectToNumberTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @param Project $value
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
     * @return Project|null
     */
    public function reverseTransform($value): ?Project
    {
        if(!$value) {
            return null;
        }

        $project = $this->em->getRepository(Project::class)->find($value);

        if($project === null) {
            throw new TransformationFailedException(sprintf('A project with id %s does not exist!', $value));
        }

        return $project;
    }
}