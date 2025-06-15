<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-product-points',
    description: 'Met à jour les points des produits existants',
)]
class UpdateProductPointsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        foreach ($products as $product) {
            if ($product->getPoints() === null) {
                // Définir un nombre de points aléatoire entre 10 et 100
                $product->setPoints(rand(10, 100));
            }
        }

        $this->entityManager->flush();

        $output->writeln('Les points des produits ont été mis à jour avec succès.');

        return Command::SUCCESS;
    }
} 