<?php

namespace App\Repository;

use App\Entity\Repas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Repas>
 *
 * @method Repas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Repas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Repas[]    findAll()
 * @method Repas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Repas::class);
    }
    public function findrepasByNom($nom)
{
    $queryBuilder = $this->createQueryBuilder('c');

    if (!empty($nom)) {
        // Perform a case-insensitive search by converting both the search query and the database field to lowercase
        $queryBuilder->where('LOWER(c.nom) LIKE :nom')
                     ->setParameter('nom', '%' . strtolower($nom) . '%');
    }

    return $queryBuilder->getQuery()->getResult();
}

    public function getRepasStatistics(): array
    {
        $clientCount = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.type = :type')
            ->setParameter('type', 'client')
            ->getQuery()
            ->getSingleScalarResult();

        $adminCount = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.type = :type')
            ->setParameter('type', 'admin')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'client' => $clientCount,
            'admin' => $adminCount,
        ];
    }
    /**
     * Fetch all Repas entities
     *
     * @return Repas[] Returns an array of Repas objects
     */
    public function selectAllRepas(): array
    {
        return $this->createQueryBuilder('r')
            ->getQuery()
            ->getResult();
    }
    public function trie()
    {
        return $this->createQueryBuilder('repas')
            ->orderBy('repas.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function trieDes()
    {
        return $this->createQueryBuilder('repas')
            ->orderBy('repas.nom', 'DESC')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Repas[] Returns an array of Repas objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Repas
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
