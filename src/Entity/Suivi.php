<?php

namespace App\Entity;

use App\Repository\SuiviRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SuiviRepository::class)
 */
class Suivi
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observation;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_user;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $heure_entree;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $heure_sortie;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $total;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getId_User(): ?int
    {
        return $this->id_user;
    }
    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }
    
    public function getHeure_Entree(): ?string
    {
        return $this->heure_entree;
    }

    public function getHeure_Sortie(): ?string
    {
        return $this->heure_sortie;
    }
    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;

        return $this;
    }
    public function setId_User(?int $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }
    public function setTotal(?string $total): self
    {
        $this->total = $total;

        return $this;
    }


    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function setHeure_Sortie(?string $heure_sortie): self
    {
        $this->heure_sortie = $heure_sortie;

        return $this;
    }

    public function setHeure_Entree(?string $heure_entree): self
    {
        $this->heure_entree = $heure_entree;

        return $this;
    }
}
