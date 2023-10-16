<?php

namespace App\Repository;

use App\Entity\GiftListTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GiftListTheme>
 *
 * @method GiftListTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method GiftListTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method GiftListTheme[]    findAll()
 * @method GiftListTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GiftListThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GiftListTheme::class);
    }

//    /**
//     * @return GiftListTheme[] Returns an array of GiftListTheme objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GiftListTheme
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
