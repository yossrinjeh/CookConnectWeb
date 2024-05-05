<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;


/**
 * Poste
 *
 * @ORM\Table(name="poste", indexes={@ORM\Index(name="fk_id_user_poste", columns={"id_user"})})
 * @ORM\Entity
 */
class Poste
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

    /**
     * @var \App\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=300, nullable=false)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=300, nullable=false)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image", type="string", length=300, nullable=true)
     */
    private $image;

    /**
     * @var string|null
     *
     * @ORM\Column(name="video", type="string", length=300, nullable=true)
     */
    private $video;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="date", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $date;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): static
    {
        $this->video = $video;

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

    public function __toString(): string
    {
        return $this->titre;
    }
}
