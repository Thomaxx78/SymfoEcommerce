<?php

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;

class MesProduitsProvider implements ProviderInterface
{
    public function __construct(
        private ProductRepository $productRepository,
        private Security $security
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        $user = $this->security->getUser();
        
        if (!$user) {
            return [];
        }

        return $this->productRepository->findBy(['createdBy' => $user]);
    }
} 