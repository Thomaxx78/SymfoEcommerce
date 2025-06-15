<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@gmail.com');
        $admin->setName('Admin');
        $admin->setFirstName('Super');
        $admin->setPoints(10000);
        $admin->setActive(true);
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@gmail.com');
        $user->setName('User');
        $user->setFirstName('Normal');
        $user->setPoints(500);
        $user->setActive(true);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        $categories = ['Musique', 'Film', 'Livre', 'Jeux', 'Autre'];
        
        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $product->setName('Produit ' . $i);
            $product->setPrice(random_int(50, 500));
            $product->setPoints(random_int(1, 250));
            $product->setCategory($categories[array_rand($categories)]);
            $product->setDescription('Description du produit ' . $i);
            $product->setCreatedBy($admin);
            $manager->persist($product);
        }

        $manager->flush();
    }
}