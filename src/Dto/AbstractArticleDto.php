<?php

namespace App\Dto;

use App\Entity\User;
use App\Entity\MediaObject;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractArticleDto
{
    #[Groups(['article:read'])]
    public ?int $id = null;

    #[Groups(['article:read', 'article:write'])]
    #[Assert\NotBlank]
    public string $title;

    #[Groups(['article:read', 'article:write'])]
    public ?\DateTimeImmutable $createdAt = null;

    #[Groups(['article:read', 'article:write'])]
    public array $content = [];

    #[Groups(['article:read', 'article:write'])]
    public ?User $author = null;

    #[Groups(['article:read', 'article:write'])]
    public ?MediaObject $thumbnail = null;

    #[Groups(['article:read', 'article:write'])]
    public ?string $status = 'draft';
}
