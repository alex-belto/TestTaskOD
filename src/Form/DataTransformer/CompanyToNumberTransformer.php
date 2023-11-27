<?php

namespace App\Form\DataTransformer;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CompanyToNumberTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @param Company|null $value
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
     * @return Company|null
     */
    public function reverseTransform($value): ?Company
    {
        if(!$value) {
            return null;
        }

        $company = $this->em->getRepository(Company::class)->find($value);

        if($company === null) {
            throw new TransformationFailedException(sprintf('A Company with id %s does not exist!', $value));
        }

        return $company;
    }
}