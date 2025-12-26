<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\State\PostStateProvider;
use App\State\PostStateProcessor;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    uriTemplate: '/posts',
    operations: [
        new GetCollection(provider: PostStateProvider::class, normalizationContext: ['groups' => ['article:read']]),
        new Post(processor: PostStateProcessor::class, denormalizationContext: ['groups' => ['article:write']]),
    ],
)]
#[ApiResource(
    uriTemplate: '/posts/{id}',
    operations: [
        new Get(provider: PostStateProvider::class, normalizationContext: ['groups' => ['article:read']]),
        new Put(
            provider: PostStateProvider::class,
            processor: PostStateProcessor::class,
            denormalizationContext: ['groups' => ['article:write']]
        ),
        new Delete(processor: PostStateProcessor::class, provider: PostStateProvider::class)
    ]
)]
class PostDto extends AbstractArticleDto
{

    /**
     * @var Comment[]
     */
    #[Groups(['article:read'])]
    public array $comments = [];
}
