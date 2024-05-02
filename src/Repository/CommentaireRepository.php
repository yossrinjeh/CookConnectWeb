<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Commentaire;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;



/**
 * @extends EntityRepository<Commentaire>
 *
 * @method Commentaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentaire[]    findAll()
 * @method Commentaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class CommentaireRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

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
            ->join('c.poste', 'p')
            ->where('p.id = :postId')
            ->setParameter('postId', $postId)
            ->getQuery()
            ->getResult();
    }

    public function selectCommentsByUserId($userId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :userId')
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

    public function getCommentCount(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
