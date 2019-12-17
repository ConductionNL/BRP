<?php

namespace App\Repository;

use App\Entity\Overlijden;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Overlijden|null find($id, $lockMode = null, $lockVersion = null)
 * @method Overlijden|null findOneBy(array $criteria, array $orderBy = null)
 * @method Overlijden[]    findAll()
 * @method Overlijden[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OverlijdenRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Overlijden::class);
    }

    // /**
    //  * @return Overlijden[] Returns an array of Overlijden objects
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
    public function findOneBySomeField($value): ?Overlijden
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
