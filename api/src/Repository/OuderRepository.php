<?php

namespace App\Repository;

use App\Entity\Ouder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ouder|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ouder|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ouder[]    findAll()
 * @method Ouder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OuderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ouder::class);
    }

    // /**
    //  * @return Ouder[] Returns an array of Ouder objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ouder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
