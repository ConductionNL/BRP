<?php

namespace App\Repository;

use App\Entity\Ingeschrevenpersoon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ingeschrevenpersoon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ingeschrevenpersoon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ingeschrevenpersoon[]    findAll()
 * @method Ingeschrevenpersoon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngeschrevenpersoonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingeschrevenpersoon::class);
    }

    public function getFamilly($bsn)
    {
        return $this->createQueryBuilder('i')
        ->leftJoin('i.kinderen', 'k')
        ->leftJoin('i.partners', 'p')
        ->leftJoin('i.ouders', 'o')
        ->where($qb->expr()->orX(
            $qb->expr()->eq('k.burgerservicenummer', $bsn),
            $qb->expr()->eq('p.burgerservicenummer', $bsn),
            $qb->expr()->eq('o.burgerservicenummer', $bsn)
        ))
        ->getQuery()
        ->getResult();
    }

    // /**
    //  * @return NatuurlijkPersoon[] Returns an array of NatuurlijkPersoon objects
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
    public function findOneBySomeField($value): ?Ingeschrevenpersoon
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
