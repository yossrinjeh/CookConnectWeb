<?php
namespace App\Repository;

use App\Entity\Ajoutingredientrequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AjoutingredientrequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ajoutingredientrequest::class);
    }

    // CUSTOM METHODS HERE
}
