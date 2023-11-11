<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employee>
 *
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function getRandomEmployee(): ?Employee
    {
        $sql = 'SELECT * FROM employee ORDER BY RANDOM() LIMIT 1';
        $query = $this->getEntityManager()->createNativeQuery($sql, new ResultSetMapping());

        $result = $query->getResult();

        // Вернуть случайную компанию или null, если нет результатов
        return empty($result) ? null : $result[0];
    }
}
