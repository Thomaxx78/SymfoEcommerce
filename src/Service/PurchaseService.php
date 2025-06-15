<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PurchaseService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private LoggerInterface $logger
    ) {
    }

    public function purchaseProduct(User $user, Product $product): bool
    {
        $this->logger->info('Début de l\'achat pour l\'utilisateur: ' . $user->getEmail());
        $this->logger->info('Points de l\'utilisateur: ' . $user->getPoints());
        $this->logger->info('Points requis: ' . $product->getPoints());

        if (!$user->isActive()) {
            $this->logger->error('Compte désactivé pour l\'utilisateur: ' . $user->getEmail());
            throw new \Exception('Votre compte est désactivé. Vous ne pouvez pas effectuer d\'achat.');
        }

        if (!$user->hasEnoughPoints($product->getPoints())) {
            $this->logger->error('Points insuffisants pour l\'utilisateur: ' . $user->getEmail());
            throw new \Exception('Vous n\'avez pas assez de points pour acheter ce produit.');
        }

        try {
            $user->deductPoints($product->getPoints());
            $this->entityManager->persist($user);
            $this->logger->info('Points déduits pour l\'utilisateur: ' . $user->getEmail());

            $notification = new Notification();
            $notification->setLabel("Vous avez acheté le produit : " . $product->getName() . " pour " . $product->getPoints() . " points.");
            $notification->setUser($user);
            $notification->setDate(new \DateTime());
            $this->entityManager->persist($notification);
            $this->logger->info('Notification créée pour l\'utilisateur: ' . $user->getEmail());

            $admin = $this->userRepository->findAdmin();
            if ($admin) {
                $this->logger->info('Admin trouvé: ' . $admin->getEmail());
                $adminNotification = new Notification();
                $adminNotification->setLabel("L'utilisateur " . $user->getEmail() . " a acheté le produit : " . $product->getName() . " pour " . $product->getPoints() . " points.");
                $adminNotification->setUser($admin);
                $adminNotification->setDate(new \DateTime());
                $this->entityManager->persist($adminNotification);
                $this->logger->info('Notification créée pour l\'admin');
            } else {
                $this->logger->error('Aucun admin trouvé dans la base de données');
            }

            $this->entityManager->flush();
            $this->logger->info('Achat terminé avec succès');
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'achat: ' . $e->getMessage());
            throw $e;
        }

        return true;
    }
}