<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\PurchaseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;

#[Route('/product')]
class ProductController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {
    }

    #[Route('/products', name: 'app_products')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        $categories = $productRepository->findAllCategories();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => array_column($categories, 'category'),
        ]);
    }

    #[Route('/{id}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/purchase', name: 'app_purchase_product', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function purchase(Product $product, PurchaseService $purchaseService, Request $request): Response
    {
        $this->logger->info('Début de la méthode purchase');
        $this->logger->info('Produit: ' . $product->getName());
        $this->logger->info('Utilisateur: ' . $this->getUser()->getUserIdentifier());
        $this->logger->info('Token CSRF reçu: ' . $request->request->get('_token'));
        
        if ($this->isCsrfTokenValid('purchase'.$product->getId(), $request->request->get('_token'))) {
            $this->logger->info('Token CSRF valide');
            try {
                $purchaseService->purchaseProduct($this->getUser(), $product);
                $this->addFlash('success', 'Produit acheté avec succès !');
                $this->logger->info('Achat réussi');
            } catch (\Exception $e) {
                $this->logger->error('Erreur lors de l\'achat: ' . $e->getMessage());
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
            }
        } else {
            $this->logger->error('Token CSRF invalide');
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        return $this->redirectToRoute('app_products');
    }
}