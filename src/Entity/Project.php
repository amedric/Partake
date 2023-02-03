<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, length: 500, nullable: true)]
    #[Assert\Length(
        max: 500,
        maxMessage: 'Description is too long, it should not exceed 500 characters',
    )]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    private ?int $projectViews = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $projectColor = null;

    #[ORM\ManyToOne]
    private ?Category $category = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column]
    private ?bool $isArchived = false;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'projects')]
    private Collection $usersSelectOnProject;

    public function __construct()
    {
        $this->usersSelectOnProject = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }


    public function getProjectViews(): ?int
    {
        return $this->projectViews;
    }

    public function setProjectViews(?int $projectViews): self
    {
        $this->projectViews = $projectViews;

        return $this;
    }

    public function getProjectColor(): ?string
    {
        return $this->projectColor;
    }

    public function setProjectColor(?string $projectColor): self
    {
        $this->projectColor = $projectColor;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersSelectOnProject(): Collection
    {
        return $this->usersSelectOnProject;
    }

    public function addUsersSelectOnProject(User $usersSelectOnProject): self
    {
        if (!$this->usersSelectOnProject->contains($usersSelectOnProject)) {
            $this->usersSelectOnProject->add($usersSelectOnProject);
        }

        return $this;
    }

    public function removeUsersSelectOnProject(User $usersSelectOnProject): self
    {
        $this->usersSelectOnProject->removeElement($usersSelectOnProject);

        return $this;
    }

}
