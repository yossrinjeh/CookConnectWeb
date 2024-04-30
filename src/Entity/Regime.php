<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Regime
 *
 * @ORM\Table(name="regime", indexes={@ORM\Index(name="id_repas", columns={"id_repas"})})
 * @ORM\Entity
 */
class Regime
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $date;

    /**
     * @var Repas
     *
     * @ORM\ManyToOne(targetEntity="Repas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_repas", referencedColumnName="id")
     * })
     */
    private $idRepas;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getIdRepas(): ?Repas
    {
        return $this->idRepas;
    }

    public function setIdRepas(?Repas $idRepas): self
    {
        $this->idRepas = $idRepas;
        return $this;
    }
    public function __toString(): string
    {
        return $this->getid(); // Assuming you have a getName() method to return a string property of the Repas entity
    }
}
