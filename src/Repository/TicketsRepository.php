<?php

namespace App\Repository;

use App\Entity\Tickets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Tickets>
 */
class TicketsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tickets::class);
    }

    //    /**
    //     * @return Tickets[] Returns an array of Tickets objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tickets
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
     
    public function findTicketsByEvenement(int $evenementId)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.evenement = :evenementId')
            ->setParameter('evenementId', $evenementId)
            ->getQuery()
            ->getResult();
    }
    public function findAllTicketsPurchasedByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user_id = :user')
            ->setParameter('user', $user)
            ->orderBy('t.date_achat', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
}
