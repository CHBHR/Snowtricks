<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Figure $figure = null;

    #[ORM\OneToOne(inversedBy: 'avatar', cascade: ['persist', 'remove'])]
    private ?Utilisateur $utilisateurId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getFigure(): ?Figure
    {
        return $this->figure;
    }

    public function setFigure(?Figure $figure): self
    {
        $this->figure = $figure;

        return $this;
    }

    public function getUtilisateurId(): ?utilisateur
    {
        return $this->utilisateurId;
    }

    public function setUtilisateurId(?utilisateur $utilisateurId): self
    {
        $this->utilisateurId = $utilisateurId;

        return $this;
    }
}
