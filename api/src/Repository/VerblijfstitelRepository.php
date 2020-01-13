<?php

namespace App\Repository;

use App\Entity\Verblijfstitel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Verblijfstitel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Verblijfstitel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Verblijfstitel[]    findAll()
 * @method Verblijfstitel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerblijfstitelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Verblijfstitel::class);
    }

    // /**
    //  * @return Verblijfstitel[] Returns an array of Verblijfstitel objects
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
    public function findOneBySomeField($value): ?Verblijfstitel
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
