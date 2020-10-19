<?php

namespace App\Repository;

use App\Entity\Waardetabel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Waardetabel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Waardetabel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Waardetabel[]    findAll()
 * @method Waardetabel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WaardetabelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Waardetabel::class);
    }

    // /**
    //  * @return Waardetabel[] Returns an array of Waardetabel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Waardetabel
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
