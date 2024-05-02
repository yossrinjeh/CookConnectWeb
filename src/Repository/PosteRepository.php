<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Poste;

use Doctrine\Persistence\ManagerRegistry;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends EntityRepository<Poste>
 *
 * @method Poste|null find($id, $lockMode = null, $lockVersion = null)
 * @method Poste|null findOneBy(array $criteria, array $orderBy = null)
 * @method Poste[]    findAll()
 * @method Poste[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class PosteRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poste::class);
    }

    public function selectPostById($postId): ?Poste
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :postId')
            ->setParameter('postId', $postId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function selectPostsByUserId($userId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function selectAllPosts()
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
    }

    public function getPostCount(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
