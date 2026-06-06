<?php

namespace App\Entity;

use App\Repository\CommandesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandesRepository::class)]
class Commandes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $achat = null;

    #[ORM\Column]
    private ?float $montant_total = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_commande = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Evenements $evenement_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAchat(): ?int
    {
        return $this->achat;
    }

    public function setAchat(int $achat): static
    {
        $this->achat = $achat;

        return $this;
    }

    public function getMontantTotal(): ?float
    {
        return $this->montant_total;
    }

    public function setMontantTotal(float $montant_total): static
    {
        $this->montant_total = $montant_total;

        return $this;
    }

    public function getDateCommande(): ?\DateTime
    {
        return $this->date_commande;
    }

    public function setDateCommande(\DateTime $date_commande): static
    {
        $this->date_commande = $date_commande;

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
}
