<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use App\Dto\PostDto;
use App\Service\DeleteArticleService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class PostStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
        #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
        private readonly ProcessorInterface $removeProcessor,
        private readonly DeleteArticleService $deleteArticle,
        private readonly EntityManagerInterface $em,
        private readonly Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        if ($operation instanceof Post || $operation instanceof Put) {
            if (!$this->security->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedException('Only admins can create posts.');
            }
        }

        // Gestion des DELETE personnalisés
        if ($operation instanceof DeleteOperationInterface) {
            $this->deleteArticle->delete($uriVariables, 'post');
            return null;
        }

        // Si ce n'est pas un DTO attendu, déléguer directement
        if (!$data instanceof PostDto) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        // Récupérer ou créer l'article
        $article = isset($uriVariables['id'])
            ? $this->em->getRepository(Article::class)->find($uriVariables['id']) ?? new Article()
            : new Article();

        // Mapper le DTO vers l'entité
        $article->setTitle($data->title);
        $article->setContent($data->content);
        $article->setCreatedAt($data->createdAt ?? new \DateTimeImmutable());
        $article->setAuthor($data->author);
        $article->setThumbnail($data->thumbnail);
        $article->setStatus($data->status ?? 'draft');
        $article->setType('post');

        // Déléguer à Api Platform pour persister et appliquer la sécurité
        $article = $this->persistProcessor->process($article, $operation, $uriVariables, $context);

        // Mettre à jour l'ID dans le DTO pour retour
        $data->id = $article->getId();

        return $data;
    }
}
