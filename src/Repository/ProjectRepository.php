<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function getRandomProject(): ?Project
    {
        $sql = 'SELECT * FROM project ORDER BY RANDOM() LIMIT 1';
        $query = $this->getEntityManager()->createNativeQuery($sql, new ResultSetMapping());

        $result = $query->getResult();

        // Вернуть случайную компанию или null, если нет результатов
        return empty($result) ? null : $result[0];
    }
}
