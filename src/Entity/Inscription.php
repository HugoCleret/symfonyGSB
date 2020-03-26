<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InscriptionRepository")
 */
class Inscription
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Visiteur", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Visiteur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Formation")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Formation;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $statut;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVisiteur(): ?Visiteur
    {
        return $this->Visiteur;
    }

    public function setVisiteur(?Visiteur $Visiteur): self
    {
        $this->Visiteur = $Visiteur;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->Formation;
    }

    public function setFormation(?Formation $Formation): self
    {
        $this->Formation = $Formation;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }
}
