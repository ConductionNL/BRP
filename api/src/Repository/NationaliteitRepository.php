<?php

namespace App\Repository;

use App\Entity\Nationaliteit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Nationaliteit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nationaliteit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nationaliteit[]    findAll()
 * @method Nationaliteit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NationaliteitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nationaliteit::class);
    }

    // /**
    //  * @return Nationaliteit[] Returns an array of Nationaliteit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Nationaliteit
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
