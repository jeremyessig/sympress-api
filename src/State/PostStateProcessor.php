<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use App\Dto\PostDto;

class PostStateProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $dto = $data;

        if (!$dto instanceof PostDto) {
            return $dto;
        }

        $article = isset($uriVariables['id'])
            ? $this->em->getRepository(Article::class)->find($uriVariables['id']) ?? new Article()
            : new Article();

        $article->setTitle($dto->title);
        $article->setContent($dto->content);
        $article->setCreatedAt($dto->createdAt ?? new \DateTimeImmutable());
        $article->setAuthor($dto->author);
        $article->setThumbnail($dto->thumbnail);
        $article->setStatus($dto->status ?? 'draft');
        $article->setType('post');

        $this->em->persist($article);
        $this->em->flush();

        $dto->id = $article->getId();

        return $dto;
    }
}
