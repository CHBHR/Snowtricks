<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(
    fields: ['email'],
    message: "L'email est déjà utilisé"
)]
#[UniqueEntity(
    fields: ['nomUtilisateur'],
    message: "Ce nom d'utilisateur est déjà pris"
)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'email', length: 50, unique: true)]
    #[Assert\Email()]
    private ?string $email = null;

    #[ORM\Column(name: 'nom_utilisateur', length: 20, unique: true)]
    #[Assert\Length(
        min: 4,
        max: 20,
        minMessage: "Le nom d'utilisateur doit être d'au moins {{ limit }} caractères",
        maxMessage: "Le nom d\'utilisateur ne peut pas être plus long que {{ limit }} caractères",
    )]
    private ?string $nomUtilisateur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(
        min: 8,
        minMessage: "Le mot de passe doit être d'au moins {{ limit }} caractères",
    )]
    #[Assert\EqualTo(
        propertyPath: 'confirmerMotDePasse')]
    private ?string $motDePasse = null;

    #[Assert\EqualTo(
        propertyPath: 'motDePasse',
        message: 'La confirmation est différente du mot de passe'
    )]
    private ?string $confirmerMotDePasse;

    #[ORM\OneToOne(mappedBy: 'utilisateurId', cascade: ['persist', 'remove'])]
    private ?Images $avatar = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 100)]
    private ?string $resetToken = null;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Commentaire::class, cascade: ['remove'])]
    private Collection $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    public function getUserIdentifier(): string
    {
        return $this->nomUtilisateur;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNomUtilisateur(): ?string
    {
        return $this->nomUtilisateur;
    }

    public function setNomUtilisateur(string $nomUtilisateur): self
    {
        $this->nomUtilisateur = $nomUtilisateur;

        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getConfirmerMotDePasse(): ?string
    {
        return $this->confirmerMotDePasse;
    }

    public function setConfirmerMotDePasse(string $confirmerMotDePasse): self
    {
        $this->confirmerMotDePasse = $confirmerMotDePasse;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->motDePasse;
    }

    public function setPassword(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getAvatar(): ?Images
    {
        return $this->avatar;
    }

    public function setAvatar(?Images $avatar): self
    {
        // Unset the owning side of the relation if necessary
        if (null === $avatar && null !== $this->avatar) {
            $this->avatar->setUtilisateurId(null);
        }

        // Set the owning side of the relation if necessary
        if (null !== $avatar && $avatar->getUtilisateurId() !== $this) {
            $avatar->setUtilisateurId($this);
        }

        $this->avatar = $avatar;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setAuteur($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if (true === $this->commentaires->removeElement($commentaire)) {
            // Set the owning side to null (unless already changed)
            if ($commentaire->getAuteur() === $this) {
                $commentaire->setAuteur(null);
            }
        }

        return $this;
    }
}
