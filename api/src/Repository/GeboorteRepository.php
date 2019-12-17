<?php

namespace App\Repository;

use App\Entity\Geboorte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Geboorte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Geboorte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Geboorte[]    findAll()
 * @method Geboorte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeboorteRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Geboorte::class);
    }

    // /**
    //  * @return Geboorte[] Returns an array of Geboorte objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Geboorte
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
