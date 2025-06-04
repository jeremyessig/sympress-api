<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use App\Dto\PostDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PostStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
        private ProcessorInterface $removeProcessor,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        if ($operation instanceof DeleteOperationInterface) {
            // On suppose que le DTO a bien un ID
            $id = $uriVariables['id'] ?? null;

            if (!$id) {
                throw new \RuntimeException('Impossible de supprimer : ID manquant.');
            }

            $article = $this->em->getRepository(Article::class)->find($id);

            if (!$article || $article->getType() !== 'post') {
                throw new \RuntimeException('Article introuvable ou de type incorrect.');
            }

            $this->em->remove($article);
            $this->em->flush();

            return null; // 204 No Content
        }
        // $data->type = 'post';

        // $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        // return $result;

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
