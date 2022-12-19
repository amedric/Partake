<?php

namespace App\Entity;

use App\Repository\IdeaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IdeaRepository::class)]
class Idea
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ideaColor = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $ideaViews = null;

    #[ORM\ManyToOne]
    private ?user $user = null;

    #[ORM\ManyToOne]
    private ?project $project = null;

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

    public function getIdeaColor(): ?string
    {
        return $this->ideaColor;
    }

    public function setIdeaColor(?string $ideaColor): self
    {
        $this->ideaColor = $ideaColor;

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

    public function getIdeaViews(): ?int
    {
        return $this->ideaViews;
    }

    public function setIdeaViews(?int $ideaViews): self
    {
        $this->ideaViews = $ideaViews;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProject(): ?project
    {
        return $this->project;
    }

    public function setProject(?project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
