<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use App\Dto\PageDto;
use App\Service\DeleteArticleService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PageStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private readonly DeleteArticleService $deleteArticle,
        private readonly Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        if ($operation instanceof Post) {
            if (!$this->security->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedException('Only admins can create posts.');
            }
        }

        if ($operation instanceof DeleteOperationInterface) {
            $this->deleteArticle->delete($uriVariables, 'page');
        }

        $dto = $data;

        if (!$dto instanceof PageDto) {
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
        $article->setType('page');

        $this->em->persist($article);
        $this->em->flush();

        $dto->id = $article->getId();

        return $dto;
    }
}
