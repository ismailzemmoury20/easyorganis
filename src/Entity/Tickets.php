<?php

namespace App\Entity;

use App\Repository\TicketsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketsRepository::class)]
class Tickets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Evenements $evenement_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\Column(length: 255)]
    private ?string $code_unique = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_achat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvenementId(): ?Evenements
    {
        return $this->evenement_id;
    }

    public function setEvenementId(?Evenements $evenement_id): static
    {
        $this->evenement_id = $evenement_id;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getCodeUnique(): ?string
    {
        return $this->code_unique;
    }

    public function setCodeUnique(string $code_unique): static
    {
        $this->code_unique = $code_unique;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateAchat(): ?\DateTime
    {
        return $this->date_achat;
    }

    public function setDateAchat(\DateTime $date_achat): static
    {
        $this->date_achat = $date_achat;

        return $this;
    }
}
