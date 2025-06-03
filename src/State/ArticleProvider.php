<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\ArticleRepository;

class ArticleProvider implements ProviderInterface
{
    public function __construct(private ArticleRepository $repo) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable|object|null
    {
        $type = str_contains($operation->getName(), 'page') ? 'page' : 'post';

        if (!empty($uriVariables['id'])) {
            $article = $this->repo->find($uriVariables['id']);
            return $article && $article->getType() === $type ? $article : null;
        }

        return $this->repo->findBy(['type' => $type]);
    }
}
