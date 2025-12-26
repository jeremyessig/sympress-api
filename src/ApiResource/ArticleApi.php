<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Article;

#[ApiResource(
    shortName: 'Article',
    stateOptions: new Options(Article::class),
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
    ]

)]
class ArticleApi
{
    public ?int $id = null;

    public ?string $title = null;

    public ?\DateTimeImmutable $createdAt = null;

    public ?array $content = null;

    public ?string $type = 'post';

    public ?string $status = 'draft';
}
