<?php

namespace App\Repository;

use App\Entity\VerblijfBuitenland;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method VerblijfBuitenland|null find($id, $lockMode = null, $lockVersion = null)
 * @method VerblijfBuitenland|null findOneBy(array $criteria, array $orderBy = null)
 * @method VerblijfBuitenland[]    findAll()
 * @method VerblijfBuitenland[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerblijfBuitenlandRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerblijfBuitenland::class);
    }

    // /**
    //  * @return VerblijfBuitenland[] Returns an array of VerblijfBuitenland objects
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
    public function findOneBySomeField($value): ?VerblijfBuitenland
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
