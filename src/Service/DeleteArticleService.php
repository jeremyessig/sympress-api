<?php

namespace App\Service;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

class DeleteArticleService implements ServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    public function delete(array $uriVariables, string $type): null
    {

        // On suppose que le DTO a bien un ID
        $id = $uriVariables['id'] ?? null;

        if (!$id) {
            throw new \RuntimeException('Impossible de supprimer : ID manquant.');
        }

        $article = $this->em->getRepository(Article::class)->find($id);

        if (!$article || $article->getType() !== $type) {
            throw new \RuntimeException('Article introuvable ou de type incorrect.');
        }

        $this->em->remove($article);
        $this->em->flush();

        return null; // 204 No Content
    }
}
