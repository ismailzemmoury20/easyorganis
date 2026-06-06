<?php

namespace App\Repository;

use App\Entity\Evenements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenements>
 */
class EvenementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenements::class);
    }

    // /**
    // * @return Evenements[] Returns an array of Evenements objects
    // */
        // public function findByExampleField($value): array
        // {
        //     return $this->createQueryBuilder('e')
        //         ->andWhere('e.exampleField = :val')
        //         ->setParameter('val', $value)
        //         ->orderBy('e.id', 'ASC')
        //         ->setMaxResults(10)
        //         ->getQuery()
        //         ->getResult()
        //     ;
        // }

        // public function findOneBySomeField($value): ?Evenements
        // {
        //     return $this->createQueryBuilder('e')
        //         ->andWhere('e.exampleField = :val')
        //         ->setParameter('val', $value)
        //         ->getQuery()
        //         ->getOneOrNullResult()
        //     ;
        // }
        public function findTicketsRestants(int $id): int
        {
            $qb = $this->createQueryBuilder('e')
            ->select('e.nombresPlaces - COUNT(t.id) AS tickets_restants')
            ->leftJoin('e.tickets', 't', 'WITH', 't.statut = :statut')
            ->setParameter('statut', 'vendu')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->groupBy('e.id');

            return (int) $qb->getQuery()->getSingleScalarResult();
        }
        public function findTicketsVendus(int $id): int
        {
            $qb = $this->createQueryBuilder('e')
            ->select('COUNT(t.id) AS tickets_vendus')
            ->leftJoin('e.tickets', 't', 'WITH', 't.statut = :statut')
            ->setParameter('statut', 'vendu')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->groupBy('e.id');

            return (int) $qb->getQuery()->getSingleScalarResult();
        }
        /**
         * @return Evenements[] Les 6 prochains événements à venir (date >= aujourd'hui)
         */
        public function findProchains(int $limit = 6): array
        {
            return $this->createQueryBuilder('e')
                ->andWhere('e.date >= :today')
                ->andWhere('e.isPublished = true')
                ->setParameter('today', new \DateTime('today'))
                ->orderBy('e.date', 'ASC')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
        }

        public function findEvenements()
        {
            return $this->createQueryBuilder('e')
                ->orderBy('e.date', 'ASC')
                ->getQuery();
        }
        public function findEvenementsFilter(?string $categorie = null, ?string $lieu = null)
        {
            $qb = $this->createQueryBuilder('e')
                ->orderBy('e.date', 'ASC');

            if ($categorie) {
                $qb->andWhere('e.categorie_id = :categorie')
                ->setParameter('categorie', $categorie);
            }

            if ($lieu) {
                $qb->andWhere('e.lieu LIKE :lieu')
                ->setParameter('lieu', '%'.$lieu.'%');
            }
            return $qb->getQuery();
        }
}
