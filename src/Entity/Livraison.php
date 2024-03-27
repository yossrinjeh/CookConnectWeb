<?php

namespace App\Entity;

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
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var int
     *
     * @ORM\Column(name="id_commande", type="integer", nullable=false)
     */
    private $idCommande;

    /**
     * @var int
     *
     * @ORM\Column(name="date", type="integer", nullable=false)
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="adresse", type="integer", nullable=false)
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

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdCommande(): ?int
    {
        return $this->idCommande;
    }

    public function setIdCommande(int $idCommande): static
    {
        $this->idCommande = $idCommande;

        return $this;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(int $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getAdresse(): ?int
    {
        return $this->adresse;
    }

    public function setAdresse(int $adresse): static
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


}
