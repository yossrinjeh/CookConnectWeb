<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Likes
 *
 * @ORM\Table(name="likes", indexes={@ORM\Index(name="fk_id_poste", columns={"poste_id"}), @ORM\Index(name="fk_id_user_like", columns={"user_id"})})
 * @ORM\Entity
 */
class Likes
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
    //  * @ORM\Column(name="user_id", type="integer", nullable=false)
    //  */
    // private $userId;

    // /**
    //  * @var int
    //  *
    //  * @ORM\Column(name="poste_id", type="integer", nullable=false)
    //  */
    // private $posteId;

    /**
     * * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * * @var \Poste
     *
     * @ORM\ManyToOne(targetEntity="Poste")
     * @ORM\JoinColumn(name="poste_id", referencedColumnName="id")
     */
    private $poste;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $date = 'CURRENT_TIMESTAMP';

    public function getId(): ?int
    {
        return $this->id;
    }

    // public function getUserId(): ?int
    // {
    //     return $this->userId;
    // }

    // public function setUserId(int $userId): static
    // {
    //     $this->userId = $userId;

    //     return $this;
    // }

    // public function getPosteId(): ?int
    // {
    //     return $this->posteId;
    // }

    // public function setPosteId(int $posteId): static
    // {
    //     $this->posteId = $posteId;

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
