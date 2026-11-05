# 🛒 TECH-SHOP - Application Web E-Commerce High-Tech

[![Démo en ligne](https://img.shields.io/badge/Demo-Live_Site-brightgreen?style=for-the-badge)](https://tech-shop.site.je/tech-shop/index.php)

> **Projet Universitaire** — 3ème année ING (Module Développement Web)
>
> **Université Abderrahmane Mira de Béjaïa** | Faculté des Sciences Exactes | Département Informatique
>
> **Réalisé par :**  GUELAACHE Hocine & BOUKHENAK Kamel

## 📌 À propos du projet

**TECH-SHOP**  est une application web e-commerce dynamique dédiée à la vente d'équipements technologiques et produits High-Tech (smartphones, ordinateurs, audio, montres connectées et accessoires).

Ce projet vise à moderniser le commerce traditionnel en offrant une vitrine numérique complète accessible $24\text{h}/24$ et $7\text{j}/7$, tout en centralisant la gestion des stocks, des commandes et du catalogue pour l'administrateur.

Le rapport complet est disponible ici : docs/Cahier des charges E-commerce.pdf

## ✨ Fonctionnalités Principales

### 👤 Espace Client / Visiteur

* **Catalogue & Navigation :** Consultation par catégories (Téléphones, Ordinateurs, Casques/Écouteurs, Montres connectées, Accessoires).

* **Moteur de recherche & Filtres :** Recherche avancée par nom, marque, prix, catégorie.

* **Fiches produits détaillées :** Description technique, prix à jour, sélection d'options (couleurs, capacités) et images réelles.

* **Gestion du panier :** Ajout, modification des quantités et suppression d'articles.

* **Commande en ligne :** Validation de la commande avec paiement à la livraison (*Cash on Delivery*).

* **Espace membre :** Inscription, connexion sécurisée, profil et historique des commandes.

* **Évaluations :** Système de notation (étoiles 1 à 5) et dépose d'avis sur les produits.

### 🛡️ Espace Administrateur

* **Gestion du catalogue (CRUD) :** Ajout, modification, suppression des produits avec téléversement d'images multiples.

* **Gestion des stocks :** Suivi temps réel des quantités et alerte sur rupture de stock.

* **Gestion des commandes :** Suivi des commandes clients et mise à jour de leur statut (*En attente*, *Livrée*).

* **Gestion des contenus :** Mise à jour des promotions, bannières et informations de catégories.

## 🛠️ Stack Technique & Outils

* **Front-end :** HTML5, CSS3, JavaScript (vanilla)

* **Back-end :** PHP (Natif)

* **Base de données :** MySQL / MariaDB

* **Environnement de développement :** XAMPP (Apache + MySQL), VS Code

* **Design & Maquettage :** Canva

* **Hébergement & Déploiement :** InfinityFree (Sous-domaine : `tech-shop.wuaze.com`) + Certificat SSL (HTTPS)

## 🗄️ Structure de la Base de Données

Le schéma relationnel comprend 6 tables principales :

1. `users` : Gestion des clients et administrateurs.

2. `categories` : Organisation des types de produits.

3. `products` : Fiches produits (prix, stock, marque, caractéristiques, images).

4. `orders` : Commandes passées par les utilisateurs.

5. `order_items` : Détail des produits rattachés à chaque commande.

6. `reviews` : Avis et notes attribués par les clients.

```
-- 1. Table utilisateurs
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(14),
    address VARCHAR(255),
    role ENUM('client', 'admin') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Table catégories
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name ENUM('Téléphone', 'Ordinateur', 'Headphones', 'Montres', 'Accessoires') NOT NULL
);

-- 3. Table produits
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    color VARCHAR(50),
    size VARCHAR(50),
    brand VARCHAR(50),
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);

-- 4. Table commandes
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('en attente', 'livrée') DEFAULT 'en attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 5. Table détails des commandes
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- 6. Table avis / reviews
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

```

## 🚀 Installation & Configuration Locale

Pour exécuter ce projet en local avec **XAMPP** :

### 1. Prérequis

* [XAMPP](https://www.apachefriends.org/) installé (avec PHP 8.x et MySQL).

* [Git](https://git-scm.com/) installé sur votre machine.

### 2. Cloner le dépôt

Déplacez-vous dans le dossier `htdocs` de XAMPP, puis clonez le projet :

```
cd C:/xampp/htdocs/
git clone https://github.com/votre-compte/tech-shop.git

```

### 3. Configurer la base de données

1. Lancez **Apache** et **MySQL** via le *XAMPP Control Panel*.

2. Ouvrez phpMyAdmin : `http://localhost/phpmyadmin/`.

3. Créez une nouvelle base de données nommée `tech_shop`.

4. Importez le fichier `database.sql` (ou exécutez le script SQL ci-dessus dans l'onglet **SQL**).

### 4. Configurer la connexion PHP

Éditez le fichier de configuration de base de données (ex: `config/db.php`) :

```
<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "tech_shop";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>

```

### 5. Lancer l'application

Ouvrez votre navigateur web et accédez à :

```
http://localhost/tech-shop

```

## 👨‍💻 Auteurs

* **GUELAACHE Hocine** 

* **BOUKHENAK Kamel** 

## 📜 Licence

Projet académique développé dans le cadre de la formation de 3ème Année Ingénieur en Informatique à l'**Université Abderrahmane Mira de Béjaïa**. Tous droits réservés.
