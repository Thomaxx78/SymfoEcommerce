<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Notification;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/products')]
#[IsGranted('ROLE_ADMIN')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_admin_product_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('admin/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'app_admin_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedBy($this->getUser());
            $entityManager->persist($product);
            $entityManager->flush();

            $notification = new Notification();
            $notification->setLabel('Nouveau produit créé : ' . $product->getName() . ' - ' . (new \DateTime())->format('d/m/Y H:i:s'));
            $notification->setUser($this->getUser());
            $entityManager->persist($notification);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit a été créé avec succès.');
            return $this->redirectToRoute('app_admin_product_index');
        }

        return $this->render('admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $notification = new Notification();
            $notification->setLabel('Produit modifié : ' . $product->getName() . ' - ' . (new \DateTime())->format('d/m/Y H:i:s'));
            $notification->setUser($this->getUser());
            $entityManager->persist($notification);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit a été modifié avec succès.');
            return $this->redirectToRoute('app_admin_product_index');
        }

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productName = $product->getName(); // Sauvegarder le nom avant la suppression
            $entityManager->remove($product);
            $entityManager->flush();

            $notification = new Notification();
            $notification->setLabel('Produit supprimé : ' . $productName . ' - ' . (new \DateTime())->format('d/m/Y H:i:s'));
            $notification->setUser($this->getUser());
            $entityManager->persist($notification);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_admin_product_index');
    }
} 