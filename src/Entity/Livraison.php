<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Livraison
 *
 * @ORM\Table(name="livraison")
 * @ORM\Entity
 */
class Livraison
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

   /**
     * @var Commande|null
     *
     * @ORM\ManyToOne(targetEntity=Commande::class)
     * @ORM\JoinColumn(name="id_commande", referencedColumnName="id", nullable=true)
     */
    private $commande;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", nullable=false)
     */
    private $adresse;

    /**
     * @var int
     *
     * @ORM\Column(name="num_tel_livreur", type="integer", nullable=false)
     */
    private $numTelLivreur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }


    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;

        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNumTelLivreur(): ?int
    {
        return $this->numTelLivreur;
    }

    public function setNumTelLivreur(int $numTelLivreur): static
    {
        $this->numTelLivreur = $numTelLivreur;

        return $this;
    }

    public function __toString(): string
    { 
        return $this->id;
    }
}
