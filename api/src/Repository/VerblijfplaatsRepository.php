<?php

namespace App\Repository;

use App\Entity\Verblijfplaats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Verblijfplaats|null find($id, $lockMode = null, $lockVersion = null)
 * @method Verblijfplaats|null findOneBy(array $criteria, array $orderBy = null)
 * @method Verblijfplaats[]    findAll()
 * @method Verblijfplaats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerblijfplaatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Verblijfplaats::class);
    }

    // /**
    //  * @return Verblijfplaats[] Returns an array of Verblijfplaats objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Verblijfplaats
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
