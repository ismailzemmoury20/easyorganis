<?php

namespace App\Entity;

use App\Repository\EvenementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementsRepository::class)]
class Evenements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column]
    private ?int $nombres_places = null;

    #[ORM\Column]
    private ?float $prix_ticket = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Categories $categorie = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isPublished = false;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $slug = null;

    /**
     * @var Collection<int, TicketsVariation>
     */
    #[ORM\OneToMany(targetEntity: TicketsVariation::class, mappedBy: 'evenement')]
    private Collection $ticketsVariations;

    public function __construct()
    {
        $this->ticketsVariations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

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

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getNombresPlaces(): ?int
    {
        return $this->nombres_places;
    }

    public function setNombresPlaces(int $nombres_places): static
    {
        $this->nombres_places = $nombres_places;

        return $this;
    }

    public function getPrixTicket(): ?float
    {
        return $this->prix_ticket;
    }

    public function setPrixTicket(float $prix_ticket): static
    {
        $this->prix_ticket = $prix_ticket;

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

    public function getCategorie(): ?Categories
    {
        return $this->categorie;
    }

    public function setCategorie(?Categories $categorie): static
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function isPublished(): bool 
    { 
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self 
    { 
        $this->isPublished = $isPublished; return $this; 
    }
    public function getSlug(): ?string 
    { 
        return $this->slug; 
    }

    public function setSlug(string $slug): self 
    { 
        $this->slug = $slug; return $this; 
    }

    /**
     * @return Collection<int, TicketsVariation>
     */
    public function getTicketsVariations(): Collection
    {
        return $this->ticketsVariations;
    }

    public function addTicketsVariation(TicketsVariation $ticketsVariation): static
    {
        if (!$this->ticketsVariations->contains($ticketsVariation)) {
            $this->ticketsVariations->add($ticketsVariation);
            $ticketsVariation->setEvenement($this);
        }

        return $this;
    }

    public function removeTicketsVariation(TicketsVariation $ticketsVariation): static
    {
        if ($this->ticketsVariations->removeElement($ticketsVariation)) {
            // set the owning side to null (unless already changed)
            if ($ticketsVariation->getEvenement() === $this) {
                $ticketsVariation->setEvenement(null);
            }
        }

        return $this;
    }

}
