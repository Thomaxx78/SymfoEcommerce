<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}/toggle-status', name: 'app_admin_user_toggle_status', methods: ['POST'])]
    public function toggleStatus(User $user, EntityManagerInterface $entityManager): Response
    {
        $newStatus = !$user->isActive();
        $user->setActive($newStatus);

        $notification = new Notification();
        $notification->setLabel($newStatus ? 
            'Votre compte a été activé - ' . (new \DateTime())->format('d/m/Y H:i:s') :
            'Votre compte a été désactivé - ' . (new \DateTime())->format('d/m/Y H:i:s')
        );
        $notification->setUser($user);
        $entityManager->persist($notification);

        $entityManager->flush();

        $this->addFlash('success', sprintf(
            'Le compte de %s a été %s avec succès.',
            $user->getEmail(),
            $newStatus ? 'activé' : 'désactivé'
        ));

        return $this->redirectToRoute('app_admin_user_index');
    }

    #[Route('/add-points-to-all', name: 'app_admin_user_add_points', methods: ['POST'])]
    public function addPointsToAll(EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $activeUsers = $userRepository->findBy(['active' => true]);
        
        foreach ($activeUsers as $user) {
            $user->setPoints($user->getPoints() + 1000);
            
            $notification = new Notification();
            $notification->setLabel("L'administrateur vous a ajouté 1000 points !");
            $notification->setUser($user);
            $entityManager->persist($notification);
        }
        
        $entityManager->flush();
        
        $this->addFlash('success', '1000 points ont été ajoutés à tous les utilisateurs actifs.');
        
        return $this->redirectToRoute('app_admin_user_index');
    }
} 