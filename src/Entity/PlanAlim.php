<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="plan_alim", indexes={@ORM\Index(name="id_regime", columns={"id_regime"})})
 * @ORM\Entity
 */
class PlanAlim
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

   /**
 * @ORM\ManyToOne(targetEntity="App\Entity\Nutrition")
 * @ORM\JoinColumn(name="nut_id", referencedColumnName="id", nullable=false)
 */
    private $nutrition;

   /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Regime")
     * @ORM\JoinColumn(name="id_regime", referencedColumnName="id", nullable=false)
     */
    private $regime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNutrition(): ?Nutrition
    {
        return $this->nutrition;
    }
  
    public function setNutrition(?Nutrition $nutrition): self
    {
        $this->nutrition = $nutrition;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function setUser(?User $user): self
    {
        $this->user = $user;
    
        return $this;
    }

    public function getRegime(): ?Regime
    {
        return $this->regime;
    }

    public function setRegime(?Regime $regime): self
    {
        $this->regime = $regime;

        return $this;
    }
}
