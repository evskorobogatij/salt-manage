<?php

namespace App\Repository\Helpers;

use App\Entity\Helpers\Os;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Os|null find($id, $lockMode = null, $lockVersion = null)
 * @method Os|null findOneBy(array $criteria, array $orderBy = null)
 * @method Os[]    findAll()
 * @method Os[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Os::class);
    }

    // /**
    //  * @return Os[] Returns an array of Os objects
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
    public function findOneBySomeField($value): ?Os
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