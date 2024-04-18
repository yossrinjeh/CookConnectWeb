<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Likes;

/**
 * @extends EntityRepository<Likes>
 *
 * @method Likes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Likes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Likes[]    findAll()
 * @method Likes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class LikesRepository extends EntityRepository
{
    public function selectLikesByPostId($postId)
    {
        return $this->createQueryBuilder('l')
            ->where('l.poste_id = :postId')
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
