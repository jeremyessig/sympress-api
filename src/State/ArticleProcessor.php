<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

class ArticleProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof Article) {
            return $data;
        }

        $type = str_contains($operation->getName(), 'page') ? 'page' : 'post';
        $data->setType($type);

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
