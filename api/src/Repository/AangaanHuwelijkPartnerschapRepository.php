<?php

namespace App\Repository;

use App\Entity\AangaanHuwelijkPartnerschap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AangaanHuwelijkPartnerschap|null find($id, $lockMode = null, $lockVersion = null)
 * @method AangaanHuwelijkPartnerschap|null findOneBy(array $criteria, array $orderBy = null)
 * @method AangaanHuwelijkPartnerschap[]    findAll()
 * @method AangaanHuwelijkPartnerschap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AangaanHuwelijkPartnerschapRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AangaanHuwelijkPartnerschap::class);
    }

    // /**
    //  * @return AangaanHuwelijkPartnerschap[] Returns an array of AangaanHuwelijkPartnerschap objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AangaanHuwelijkPartnerschap
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
