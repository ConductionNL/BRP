<?php

namespace App\Repository;

use App\Entity\OpschortingBijhouding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OpschortingBijhouding|null find($id, $lockMode = null, $lockVersion = null)
 * @method OpschortingBijhouding|null findOneBy(array $criteria, array $orderBy = null)
 * @method OpschortingBijhouding[]    findAll()
 * @method OpschortingBijhouding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OpschortingBijhoudingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpschortingBijhouding::class);
    }

    // /**
    //  * @return OpschortingBijhouding[] Returns an array of OpschortingBijhouding objects
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
    public function findOneBySomeField($value): ?OpschortingBijhouding
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
