<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\ArticleRepository;
use App\Entity\Article;
use App\Dto\PostDto;

class PostStateProvider implements ProviderInterface
{
    public function __construct(private ArticleRepository $repo) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable|object|null
    {
        if (isset($uriVariables['id'])) {
            $article = $this->repo->find($uriVariables['id']);
            return $article && $article->getType() === 'post'
                ? $this->toDto($article)
                : null;
        }

        return array_map([$this, 'toDto'], $this->repo->findBy(['type' => 'post']));
    }

    private function toDto(Article $article): PostDto
    {
        $dto = new PostDto();
        $dto->id = $article->getId();
        $dto->title = $article->getTitle();
        $dto->createdAt = $article->getCreatedAt();
        $dto->content = $article->getContent();
        $dto->author = $article->getAuthor();
        $dto->thumbnail = $article->getThumbnail();
        $dto->status = $article->getStatus();
        $dto->comments = $article->getComments()->toArray();

        return $dto;
    }
}
