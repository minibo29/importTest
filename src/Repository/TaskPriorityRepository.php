<?php

namespace App\Repository;

use App\Entity\TaskPriority;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskPriority|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskPriority|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskPriority[]    findAll()
 * @method TaskPriority[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskPriorityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskPriority::class);
    }

    // /**
    //  * @return TaskPriority[] Returns an array of TaskPriority objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TaskPriority
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
