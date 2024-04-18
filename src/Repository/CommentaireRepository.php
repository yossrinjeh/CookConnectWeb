<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Commentaire;

/**
 * @extends EntityRepository<Commentaire>
 *
 * @method Commentaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentaire[]    findAll()
 * @method Commentaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class CommentaireRepository extends EntityRepository
{
    public function selectCommentById($commentId): ?Commentaire
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :commentId')
            ->setParameter('commentId', $commentId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function selectCommentsByPostId($postId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id_poste = :postId')
            ->setParameter('postId', $postId)
            ->getQuery()
            ->getResult();
    }

    public function selectCommentsByUserId($userId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id_user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function selectAllComments()
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult();
    }
}
