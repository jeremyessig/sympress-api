<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\State\PageStateProvider;
use App\State\PageStateProcessor;

#[ApiResource(
    uriTemplate: '/pages',
    operations: [
        new GetCollection(provider: PageStateProvider::class, normalizationContext: ['groups' => ['article:read']]),
        new Post(processor: PageStateProcessor::class, denormalizationContext: ['groups' => ['article:write']])
    ]
)]
#[ApiResource(
    uriTemplate: '/pages/{id}',
    operations: [
        new Get(provider: PageStateProvider::class, normalizationContext: ['groups' => ['article:read']]),
        new Put(processor: PageStateProcessor::class, denormalizationContext: ['groups' => ['article:write']]),
        new Delete()
    ]
)]
class PageDto extends AbstractArticleDto {}
