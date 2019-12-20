<?php

namespace App\Repository;

use App\Entity\Gezagsverhouding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Gezagsverhouding|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gezagsverhouding|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gezagsverhouding[]    findAll()
 * @method Gezagsverhouding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GezagsverhoudingRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gezagsverhouding::class);
    }

    // /**
    //  * @return Gezagsverhouding[] Returns an array of Gezagsverhouding objects
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
    public function findOneBySomeField($value): ?Gezagsverhouding
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
