<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\ArticleRepository;
use App\Entity\Article;
use App\Dto\PageDto;

class PageStateProvider implements ProviderInterface
{
    public function __construct(private ArticleRepository $repo) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable|object|null
    {
        if (isset($uriVariables['id'])) {
            $article = $this->repo->find($uriVariables['id']);
            return $article && $article->getType() === 'page'
                ? $this->toDto($article)
                : null;
        }

        return array_map([$this, 'toDto'], $this->repo->findBy(['type' => 'page']));
    }

    private function toDto(Article $article): PageDto
    {
        $dto = new PageDto();
        $dto->id = $article->getId();
        $dto->title = $article->getTitle();
        $dto->createdAt = $article->getCreatedAt();
        $dto->content = $article->getContent();
        $dto->author = $article->getAuthor();
        $dto->thumbnail = $article->getThumbnail();
        $dto->status = $article->getStatus();

        return $dto;
    }
}
