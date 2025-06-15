# 🛍️ FauxPrix - E-commerce avec Système de Points

![Symfony](https://img.shields.io/badge/Symfony-6.4-000000?style=for-the-badge&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap)

FauxPrix est une plateforme e-commerce moderne développée avec Symfony, offrant une expérience d'achat unique basée sur un système de points. Les utilisateurs peuvent accumuler et dépenser des points pour acheter des produits.

## ✨ Fonctionnalités

- 🛒 Système de points pour les achats
- 👤 Gestion des utilisateurs et des rôles
- 🔐 Authentification sécurisée
- 👨‍💼 Interface d'administration
- 📱 Design responsive

## 🚀 Prérequis

- PHP 8.2 ou supérieur
- Composer
- MySQL 8.0 ou supérieur
- Symfony CLI
- Node.js et npm (pour les assets)

## 📦 Installation

1. **Cloner le repository**
   ```bash
   git clone [URL_DU_REPO]
   cd ecommerce_symfony
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer la base de données**
   - Créer une base de données MySQL
   - Copier le fichier `.env` en `.env.local`
   - Modifier les variables d'environnement dans `.env.local` :
     ```env
     DATABASE_URL="mysql://user:password@127.0.0.1:3306/symfony_eco?serverVersion=8.0"
     ```

4. **Créer la base de données et les tables**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Charger les fixtures (données de test)**
   ```bash
   php bin/console doctrine:fixtures:load
   ```

## 🏃‍♂️ Lancer le projet

1. **Démarrer le serveur Symfony**
   ```bash
   symfony server:start
   ```

2. **Accéder à l'application**
   Ouvrez votre navigateur et accédez à `http://localhost:8000`

## 👥 Comptes de test

Deux comptes sont créés par défaut :

### Administrateur
- Email : admin@gmail.com
- Mot de passe : password

### Utilisateur standard
- Email : user@gmail.com
- Mot de passe : password

## 🛠️ Structure du projet

```
ecommerce_symfony/
├── src/
│   ├── Controller/    # Contrôleurs de l'application
│   ├── Entity/        # Entités Doctrine
│   ├── Repository/    # Repositories pour les requêtes
│   └── DataFixtures/  # Données de test
├── templates/         # Templates Twig
├── public/           # Assets publics
└── config/           # Configuration Symfony
```

## 🔧 Commandes utiles

- Créer une migration : `php bin/console make:migration`
- Exécuter les migrations : `php bin/console doctrine:migrations:migrate`
- Vider le cache : `php bin/console cache:clear`
- Créer un utilisateur : `php bin/console app:create-user`

## 👨‍💻 Auteur

Thomas Filhol