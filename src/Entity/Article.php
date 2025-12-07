<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ArticleRepository;
use App\State\ArticleProcessor;
use App\State\ArticleProvider;
use App\State\ArticleStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

// #[ApiResource(
//     security: "is_granted('PUBLIC_ACCESS')",
//     processor: ArticleStateProcessor::class,
//     normalizationContext: ['groups' => ['article:read']]
// )]
// #[Get()]
// #[Put(security: "is_granted('ROLE_ADMIN') or object.owner == user")]
// #[GetCollection()]
// #[Post(security: "is_granted('ROLE_ADMIN')")]
// #[Patch(security: "is_granted('ROLE_ADMIN') or object.owner == user")]


#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[Groups(['article:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['article:read'])]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups(['article:read'])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;


    #[Groups(['article:read'])]
    #[ORM\Column(nullable: true)]
    private ?array $content = null;

    #[Groups(['article:read'])]
    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?User $author = null;

    #[Groups(['article:read'])]
    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?MediaObject $thumbnail = null;

    #[Groups(['article:read'])]
    #[ORM\Column(length: 255)]
    private ?string $type = 'post';

    #[Groups(['article:read'])]
    #[ORM\Column(length: 255)]
    private ?string $status = 'draft';

    /**
     * @var Collection<int, Comment>
     */
    #[Groups(['article:read'])]
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'article', orphanRemoval: true)]
    private Collection $comments;


    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getThumbnail(): ?MediaObject
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?MediaObject $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    public function getContent(): ?array
    {
        return $this->content;
    }

    public function setContent(?array $content): static
    {
        $this->content = $content;

        return $this;
    }
}
