<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;
use App\Entity\Poste;


/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire", indexes={@ORM\Index(name="fk_id_poste_commentaire", columns={"id_poste"}), @ORM\Index(name="fk_id_user_commentaire", columns={"id_user"})})
 * @ORM\Entity
 */
class Commentaire
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    // /**
    //  * @var int
    //  *
    //  * @ORM\Column(name="id_user", type="integer", nullable=false)
    //  */
    // private $idUser;

    // /**
    //  * @var int
    //  *
    //  * @ORM\Column(name="id_poste", type="integer", nullable=false)
    //  */
    // private $idPoste;

    /**
     * @var \App\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \App\Entity\Poste
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Poste")
     * @ORM\JoinColumn(name="id_poste", referencedColumnName="id")
     */
    private $poste;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=300, nullable=false)
     */
    private $commentaire;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private ?\DateTimeInterface $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    // public function getIdUser(): ?int
    // {
    //     return $this->idUser;
    // }

    // public function setIdUser(int $idUser): static
    // {
    //     $this->idUser = $idUser;

    //     return $this;
    // }

    // public function getIdPoste(): ?int
    // {
    //     return $this->idPoste;
    // }

    // public function setIdPoste(int $idPoste): static
    // {
    //     $this->idPoste = $idPoste;

    //     return $this;
    // }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getPoste(): ?Poste
    {
        return $this->poste;
    }

    public function setPoste(?Poste $poste): self
    {
        $this->poste = $poste;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
