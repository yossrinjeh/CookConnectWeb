<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Likes;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Likes>
 *
 * @method Likes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Likes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Likes[]    findAll()
 * @method Likes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class LikesRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Likes::class);
    }

    public function selectLikesByPostId($postId)
    {
        return $this->createQueryBuilder('c')
            ->join('c.poste', 'p')
            ->where('p.id = :postId')
            ->setParameter('postId', $postId)
            ->getQuery()
            ->getResult();
    }

    public function selectLikesByUserId($userId)
    {
        return $this->createQueryBuilder('l')
            ->where('l.user_id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function selectAllLikes()
    {
        return $this->createQueryBuilder('l')
            ->getQuery()
            ->getResult();
    }
}
