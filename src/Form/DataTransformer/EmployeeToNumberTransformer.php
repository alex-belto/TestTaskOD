<?php

namespace App\Form\DataTransformer;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EmployeeToNumberTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @param Employee|null $value
     * @return string
     */
    public function transform($value): string
    {
        if($value === null) {
            return '';
        }

        return $value->getId();
    }

    public function reverseTransform($value): ?Employee
    {
        if(!$value) {
            return null;
        }

        $employee = $this->em->getRepository(Employee::class)->find($value);

        if($employee === null) {
            throw new TransformationFailedException(sprintf('An employee with id %s does not exist!', $value));
        }

        return $employee;
    }
}