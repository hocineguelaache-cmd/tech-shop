-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 11 mai 2026 à 21:37
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `techshop`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`category_id`, `name`) VALUES
(1, 'Téléphones'),
(2, 'Ordinateurs'),
(3, 'Casques'),
(4, 'Montres'),
(5, 'Accessoires');

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total`, `status`, `created_at`) VALUES
(3, 3, 2548.00, 'pending', '2026-05-05 07:25:51'),
(4, 3, 4398.00, 'pending', '2026-05-05 07:32:19'),
(5, 3, 349.00, 'pending', '2026-05-05 07:32:25'),
(6, 3, 2199.90, 'delivered', '2026-05-05 07:33:26'),
(7, 3, 1099.00, 'pending', '2026-05-05 14:28:27'),
(8, 3, 1799.00, 'pending', '2026-05-05 14:30:28'),
(9, 3, 1099.00, 'pending', '2026-05-05 15:02:31'),
(10, 3, 2198.00, 'delivered', '2026-05-05 15:12:12'),
(11, 3, 133376.00, 'pending', '2026-05-05 15:42:35'),
(12, 3, 0.90, 'cancelled', '2026-05-05 18:27:45'),
(13, 3, 2398.00, 'pending', '2026-05-10 07:50:40'),
(14, NULL, 1099.00, 'pending', '2026-05-10 16:00:20');

-- --------------------------------------------------------

--
-- Structure de la table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(5, 3, NULL, 1, 2199.00),
(6, 3, NULL, 1, 349.00),
(7, 4, NULL, 2, 2199.00),
(8, 5, NULL, 1, 349.00),
(9, 6, NULL, 1, 2199.00),
(10, 6, NULL, 1, 0.90),
(11, 7, NULL, 1, 1099.00),
(12, 8, NULL, 1, 1799.00),
(13, 9, NULL, 1, 1099.00),
(14, 10, NULL, 2, 1099.00),
(15, 11, NULL, 4, 33344.00),
(16, 12, NULL, 1, 0.90),
(17, 13, 2, 2, 1199.00),
(18, 14, 25, 1, 1099.00);

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `color` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `name`, `description`, `price`, `stock`, `color`, `size`, `brand`, `image`, `created_at`) VALUES
(1, 1, 'iPhone 15 Pro Max', 'Le smartphone Apple le plus puissant avec la puce A17 Pro, un écran Super Retina XDR de 6,7 pouces, un système de caméra pro 48 MP et un châssis en titane.', 1299.00, 15, NULL, NULL, 'Apple', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-naturaltitanium?wid=800&hei=800&fmt=jpeg&qlt=90', '2026-05-09 08:48:26'),
(2, 1, 'Samsung Galaxy S24 Ultra', 'Le fleuron de Samsung avec le stylet S Pen intégré, un capteur photo 200 MP, un écran Dynamic AMOLED 2X de 6,8 pouces et la puce Snapdragon 8 Gen 3.', 1199.00, 20, '', '', 'Samsung', 'https://images.unsplash.com/photo-1709744722656-9b850470293f?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8U2Ftc3VuZyUyMEdhbGF4eSUyMFMyNCUyMFVsdHJhfGVufDB8fDB8fHww', '2026-05-09 08:48:26'),
(3, 1, 'Google Pixel 9 Pro', 'L\'expérience Android pure avec Google AI intégré, photo computational avancée, écran OLED 6,3 pouces 120 Hz et 7 ans de mises à jour garanties.', 999.00, 12, '', '', 'Google', 'https://images.unsplash.com/photo-1724322535079-11b08f7f5c88?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8R29vZ2xlJTIwUGl4ZWwlMjA5JTIwUHJvfGVufDB8fDB8fHww', '2026-05-09 08:48:26'),
(4, 1, 'OnePlus 12', 'Performances de flagship avec charge rapide 100W SUPERVOOC, écran Fluid AMOLED 6,82 pouces 120 Hz, triple caméra Hasselblad et batterie 5400 mAh.', 799.00, 18, '', '', 'OnePlus', 'https://images.unsplash.com/photo-1772683828787-4101fbd26e91?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fE9uZVBsdXMlMjAxMnxlbnwwfHwwfHx8MA%3D%3D', '2026-05-09 08:48:26'),
(6, 2, 'MacBook Pro 14\" M3 Pro', 'Puissance professionnelle avec la puce M3 Pro, écran Liquid Retina XDR 14,2 pouces, jusqu\'à 22h d\'autonomie, 18 Go RAM unifiée et SSD 512 Go.', 2199.00, 8, NULL, NULL, 'Apple', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/mbp14-spacegray-select-202310?wid=800&hei=800&fmt=jpeg&qlt=90', '2026-05-09 08:48:26'),
(7, 2, 'Dell XPS 15', 'Ultrabook premium avec écran OLED 3,5K tactile, Intel Core i9-13900H, RTX 4060, 32 Go RAM DDR5 et design aluminium ultra-fin.', 1899.00, 6, '', '', 'Dell', 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fERlbGwlMjBYUFMlMjAxNXxlbnwwfHwwfHx8MA%3D%3D', '2026-05-09 08:48:26'),
(8, 2, 'ASUS ROG Zephyrus G14', 'Laptop gaming compact avec AMD Ryzen 9 8945HS, RTX 4070, écran QHD 165 Hz, 32 Go RAM et rétroéclairage AniMe Matrix LED.', 1649.00, 9, '', '', 'ASUS', 'https://images.unsplash.com/photo-1771015310937-6754da25e49a?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTN8fEFTVVMlMjBST0clMjBaZXBoeXJ1cyUyMEcxNHxlbnwwfHwwfHx8MA%3D%3D', '2026-05-09 08:48:26'),
(9, 2, 'Microsoft Surface Laptop 5', 'Élégance et performance avec Intel Core i7, 16 Go LPDDR5X, SSD 512 Go, écran PixelSense 13,5 pouces et Windows 11 Home.', 1299.00, 11, '', '', 'Microsoft', 'https://images.unsplash.com/photo-1727132527904-528bb0afb370?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTN8fE1pY3Jvc29mdCUyMFN1cmZhY2UlMjBMYXB0b3AlMjA1fGVufDB8fDB8fHww', '2026-05-09 08:48:26'),
(10, 2, 'Lenovo ThinkPad X1 Carbon Gen 12', 'Ultraportable business légendaire : 1,12 kg, Intel Core Ultra 7, 32 Go LPDDR5, 1 To SSD, 4G LTE et certification militaire MIL-SPEC.', 1749.00, 7, '', '', 'Lenovo', 'https://images.unsplash.com/photo-1763162410742-1d0097cea556?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8TGVub3ZvJTIwVGhpbmtQYWQlMjBYMSUyMENhcmJvbiUyMEdlbiUyMDEyfGVufDB8fDB8fHww', '2026-05-09 08:48:26'),
(11, 3, 'Sony WH-1000XM5', 'Référence absolue en réduction de bruit active, 30h d\'autonomie, son Hi-Res Audio, microphones multidirectionnels et confort supérieur.', 349.00, 25, '', '', 'Sony', 'https://images.unsplash.com/photo-1612858250170-dd5b2e82a8b8?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTV8fFNvbnklMjBXSC0xMDAwWE01fGVufDB8fDB8fHww', '2026-05-09 08:48:26'),
(12, 3, 'Apple AirPods Max', 'Casque premium Apple avec réduction de bruit H2, audio spatial personnalisé, coussins en maille et boîtier en aluminium anodisé. Son 3D immersif.', 549.00, 14, NULL, NULL, 'Apple', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/airpods-max-select-202409-midnight?wid=800&hei=800&fmt=jpeg&qlt=90', '2026-05-09 08:48:26'),
(13, 3, 'Bose QuietComfort 45', 'Confort exceptionnel et ANC de haute qualité, 24h d\'autonomie, mode Aware pour entendre l\'environnement, connexion multipoint et appels clairs.', 329.00, 20, '', '', 'Bose', 'https://images.unsplash.com/photo-1657223143975-d29d7959a70f?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8Qm9zZSUyMFF1aWV0Q29tZm9ydCUyMDQ1fGVufDB8fDB8fHww', '2026-05-09 08:48:26'),
(14, 3, 'Sennheiser Momentum 4 Wireless', 'Son audiophile avec ANC adaptatif, 60h d\'autonomie record, charge rapide, égaliseur personnalisable et design pliable élégant.', 299.00, 17, '', '', 'Sennheiser', 'https://images.unsplash.com/photo-1594215741864-6024bef4f3e3?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8U2VubmhlaXNlciUyME1vbWVudHVtJTIwNCUyMFdpcmVsZXNzfGVufDB8fDB8fHww', '2026-05-09 08:48:26'),
(15, 3, 'Jabra Evolve2 85', 'Casque professionnel avec 8 microphones, ANC hybride à 3 niveaux, 37h d\'autonomie et certification Microsoft Teams pour réunions impeccables.', 449.00, 12, '', '', 'Jabra', 'https://media.istockphoto.com/id/1958221057/fr/photo/casque-sans-fil-noir-sur-un-bureau-noir-pr%C3%A8s-dun-ordinateur-portable.webp?a=1&b=1&s=612x612&w=0&k=20&c=JRP_y_z3s-anmDOsWnzfXAiISx0kOI5dp7qI3DjjGrE=', '2026-05-09 08:48:26'),
(16, 4, 'Apple Watch Series 9', 'La montre connectée la plus avancée : puce S9, écran Always-On Retina 45 mm, détection des accidents, ECG, oxymètre et geste Double Tap.', 429.00, 22, '', '', 'Apple', 'https://images.unsplash.com/photo-1649494652462-78b9628370a9?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fEFwcGxlJTIwV2F0Y2glMjBTZXJpZXMlMjA5fGVufDB8fDB8fHww', '2026-05-09 08:48:26'),
(17, 4, 'Samsung Galaxy Watch 7', 'Suivi santé avancé avec analyse du glucose sans piqûre, puissance Exynos W1000, boîtier 44 mm, autonomie 40h et Galaxy AI intégré.', 299.00, 19, '', '', 'Samsung', 'https://images.unsplash.com/photo-1722153105551-cfea928e80de?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8U2Ftc3VuZyUyMEdhbGF4eSUyMFdhdGNoJTIwN3xlbnwwfHwwfHx8MA%3D%3D', '2026-05-09 08:48:26'),
(18, 4, 'Garmin Fenix 7X Solar', 'Montre outdoor ultime avec recharge solaire, GPS multi-bande, cartographie TopoActive, suivi multisport 100+ activités et autonomie 28 jours.', 899.00, 8, '', '', 'Garmin', 'https://images.unsplash.com/photo-1593198805047-b97ea2348680?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTN8fEdhcm1pbiUyMEZlbml4JTIwN1glMjBTb2xhcnxlbnwwfHwwfHx8MA%3D%3D', '2026-05-09 08:48:26'),
(19, 4, 'Fitbit Sense 2', 'Santé au poignet avec détection du stress EDA, ECG, SpO2, température cutanée, GPS intégré, NFC et 6 jours d\'autonomie.', 249.00, 16, '', '', 'Fitbit', 'https://images.unsplash.com/photo-1557045157-0c9880d11677?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Rml0Yml0JTIwU2Vuc2UlMjAyfGVufDB8fDB8fHww', '2026-05-09 08:48:26'),
(20, 5, 'AirPods Pro 2ème génération', 'Réduction de bruit active H2, audio adaptatif, mode Transparence, résistance IP54, boîtier MagSafe avec haut-parleur et jusqu\'à 30h d\'autonomie totale.', 279.00, 30, NULL, NULL, 'Apple', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/MQD83?wid=800&hei=800&fmt=jpeg&qlt=90', '2026-05-09 08:48:26'),
(21, 5, 'Anker MagGo 3-en-1', 'Station de charge MagSafe 3-en-1 pour iPhone, Apple Watch et AirPods. 15W MagSafe, charge rapide simultanée, design pliable de voyage.', 89.00, 35, '', '', 'Anker', 'https://images.unsplash.com/photo-1595756630832-848909a39597?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8QW5rZXIlMjBNYWdHbyUyMDMtZW4tMXxlbnwwfHwwfHx8MA%3D%3D', '2026-05-09 08:48:26'),
(22, 5, 'Samsung 990 Pro SSD 2 To', 'SSD NVMe PCIe 4.0 ultra-rapide : 7450 Mo/s en lecture, 6900 Mo/s en écriture, parfait pour gaming et création de contenu professionnel.', 179.00, 40, '', '', 'Samsung', 'https://images.unsplash.com/photo-1734605022656-937e1cf0839d?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8U2Ftc3VuZyUyMDk5MCUyMFBybyUyMFNTRCUyMDIlMjBUb3xlbnwwfHwwfHx8MA%3D%3D', '2026-05-09 08:48:26'),
(23, 5, 'Logitech MX Master 3S', 'Souris sans fil professionnelle avec MagSpeed électromagnétique, 8000 DPI, suivi sur verre, Easy-Switch 3 appareils et silence des clics.', 109.00, 28, NULL, NULL, 'Logitech', 'https://resource.logitech.com/w_800,c_lpad,ar_1:1,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/logitech/en/products/mice/mx-master-3s/gallery/mx-master-3s-mouse-top-view-graphite.png', '2026-05-09 08:48:26'),
(24, 5, 'Baseus 70W GaN Chargeur', 'Chargeur compact GaN 70W avec 2 ports USB-C et 1 USB-A. Charge un MacBook, iPhone et iPad simultanément. Petit comme une boîte d\'allumettes.', 49.00, 50, 'black', '', 'Baseus', 'https://images.unsplash.com/photo-1763161786687-43d0c9babdf0?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8QmFzZXVzJTIwNjVXJTIwR2FOJTIwQ2hhcmdldXJ8ZW58MHx8MHx8fDA%3D', '2026-05-09 08:48:26'),
(25, 1, 'iPhone 14 Pro Max', 'Le smartphone Apple le plus puissant avec la puce A17 Pro, un écran Super Retina XDR de 6,7 pouces, un système de caméra pro 48 MP et un châssis en titane.', 1099.00, 4, 'black red ', '', 'Apple', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-blacktitanium?wid=800&hei=800&fmt=jpeg&qlt=90', '2026-05-09 10:55:27');

-- --------------------------------------------------------

--
-- Structure de la table `product_colors`
--

CREATE TABLE `product_colors` (
  `color_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_name` varchar(100) NOT NULL,
  `color_hex` varchar(7) NOT NULL DEFAULT '#000000',
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_url`, `sort_order`) VALUES
(1, 1, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-naturaltitanium?wid=800&hei=800&fmt=jpeg&qlt=90', 0),
(6, 6, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/mbp14-spacegray-select-202310?wid=800&hei=800&fmt=jpeg&qlt=90', 0),
(12, 12, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/airpods-max-select-202409-midnight?wid=800&hei=800&fmt=jpeg&qlt=90', 0),
(20, 20, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/MQD83?wid=800&hei=800&fmt=jpeg&qlt=90', 0),
(23, 23, 'https://resource.logitech.com/w_800,c_lpad,ar_1:1,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/logitech/en/products/mice/mx-master-3s/gallery/mx-master-3s-mouse-top-view-graphite.png', 0),
(32, 1, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-bluetitanium?wid=800&hei=800&fmt=jpeg&qlt=90', 1),
(33, 1, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-whitetitanium?wid=800&hei=800&fmt=jpeg&qlt=90', 2),
(34, 1, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-blacktitanium?wid=800&hei=800&fmt=jpeg&qlt=90', 3),
(35, 6, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/mbp14-silver-select-202310?wid=800&hei=800&fmt=jpeg&qlt=90', 1),
(36, 6, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/mbp14-spacegray-gallery1-202310?wid=800&hei=800&fmt=jpeg&qlt=90', 2),
(41, 24, 'https://images.unsplash.com/photo-1763161786687-43d0c9babdf0?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8QmFzZXVzJTIwNjVXJTIwR2FOJTIwQ2hhcmdldXJ8ZW58MHx8MHx8fDA%3D', 0),
(42, 22, 'https://images.unsplash.com/photo-1734605022656-937e1cf0839d?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8U2Ftc3VuZyUyMDk5MCUyMFBybyUyMFNTRCUyMDIlMjBUb3xlbnwwfHwwfHx8MA%3D%3D', 0),
(43, 22, 'https://images.unsplash.com/photo-1669480380758-4b163a33f6f9?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8U2Ftc3VuZyUyMDk5MCUyMFBybyUyMFNTRCUyMDIlMjBUb3xlbnwwfHwwfHx8MA%3D%3D', 1),
(44, 21, 'https://images.unsplash.com/photo-1595756630832-848909a39597?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8QW5rZXIlMjBNYWdHbyUyMDMtZW4tMXxlbnwwfHwwfHx8MA%3D%3D', 0),
(45, 21, 'https://images.unsplash.com/photo-1596877445530-ad74838754c6?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8QW5rZXIlMjBNYWdHbyUyMDMtZW4tMXxlbnwwfHwwfHx8MA%3D%3D', 1),
(46, 19, 'https://images.unsplash.com/photo-1557045157-0c9880d11677?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Rml0Yml0JTIwU2Vuc2UlMjAyfGVufDB8fDB8fHww', 0),
(47, 19, 'https://images.unsplash.com/photo-1557045136-50ed3874553c?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8Rml0Yml0JTIwU2Vuc2UlMjAyfGVufDB8fDB8fHww', 1),
(48, 18, 'https://images.unsplash.com/photo-1593198805047-b97ea2348680?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTN8fEdhcm1pbiUyMEZlbml4JTIwN1glMjBTb2xhcnxlbnwwfHwwfHx8MA%3D%3D', 0),
(49, 18, 'https://images.unsplash.com/photo-1750776100861-30c172651817?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8R2FybWluJTIwRmVuaXglMjA3WCUyMFNvbGFyfGVufDB8fDB8fHww', 1),
(50, 2, 'https://images.unsplash.com/photo-1709744722656-9b850470293f?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8U2Ftc3VuZyUyMEdhbGF4eSUyMFMyNCUyMFVsdHJhfGVufDB8fDB8fHww', 0),
(51, 2, 'https://images.unsplash.com/photo-1705585175110-d25f92c183aa?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8U2Ftc3VuZyUyMEdhbGF4eSUyMFMyNCUyMFVsdHJhfGVufDB8fDB8fHww', 1),
(52, 2, 'https://images.unsplash.com/photo-1705585174953-9b2aa8afc174?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8U2Ftc3VuZyUyMEdhbGF4eSUyMFMyNCUyMFVsdHJhfGVufDB8fDB8fHww', 2),
(53, 3, 'https://images.unsplash.com/photo-1724322535079-11b08f7f5c88?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8R29vZ2xlJTIwUGl4ZWwlMjA5JTIwUHJvfGVufDB8fDB8fHww', 0),
(54, 17, 'https://images.unsplash.com/photo-1722153105551-cfea928e80de?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8U2Ftc3VuZyUyMEdhbGF4eSUyMFdhdGNoJTIwN3xlbnwwfHwwfHx8MA%3D%3D', 0),
(55, 16, 'https://images.unsplash.com/photo-1649494652462-78b9628370a9?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fEFwcGxlJTIwV2F0Y2glMjBTZXJpZXMlMjA5fGVufDB8fDB8fHww', 0),
(56, 16, 'https://images.unsplash.com/photo-1762768722543-289c49f0050a?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTV8fEFwcGxlJTIwV2F0Y2glMjBTZXJpZXMlMjA5fGVufDB8fDB8fHww', 1),
(57, 4, 'https://images.unsplash.com/photo-1772683828787-4101fbd26e91?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fE9uZVBsdXMlMjAxMnxlbnwwfHwwfHx8MA%3D%3D', 0),
(58, 4, 'https://images.unsplash.com/photo-1772683828844-15dca7c553b8?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fE9uZVBsdXMlMjAxMnxlbnwwfHwwfHx8MA%3D%3D', 1),
(59, 15, 'https://media.istockphoto.com/id/1958221057/fr/photo/casque-sans-fil-noir-sur-un-bureau-noir-pr%C3%A8s-dun-ordinateur-portable.webp?a=1&b=1&s=612x612&w=0&k=20&c=JRP_y_z3s-anmDOsWnzfXAiISx0kOI5dp7qI3DjjGrE=', 0),
(60, 15, 'https://images.unsplash.com/photo-1624978960894-bed9218acd39?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8SmFicmElMjBFdm9sdmUyJTIwODV8ZW58MHx8MHx8fDA%3D', 1),
(61, 14, 'https://images.unsplash.com/photo-1594215741864-6024bef4f3e3?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8U2VubmhlaXNlciUyME1vbWVudHVtJTIwNCUyMFdpcmVsZXNzfGVufDB8fDB8fHww', 0),
(62, 14, 'https://images.unsplash.com/photo-1754142654796-cafbcb9f633b?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8U2VubmhlaXNlciUyME1vbWVudHVtJTIwNCUyMFdpcmVsZXNzfGVufDB8fDB8fHww', 1),
(65, 13, 'https://images.unsplash.com/photo-1657223143975-d29d7959a70f?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8Qm9zZSUyMFF1aWV0Q29tZm9ydCUyMDQ1fGVufDB8fDB8fHww', 0),
(66, 13, 'https://images.unsplash.com/photo-1566286378558-5d52a6710b6e?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8Qm9zZSUyMFF1aWV0Q29tZm9ydCUyMDQ1fGVufDB8fDB8fHww', 1),
(67, 11, 'https://images.unsplash.com/photo-1612858250170-dd5b2e82a8b8?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTV8fFNvbnklMjBXSC0xMDAwWE01fGVufDB8fDB8fHww', 0),
(68, 11, 'https://images.unsplash.com/photo-1550009158-baab1dbdb5cc?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTJ8fFNvbnklMjBXSC0xMDAwWE01fGVufDB8fDB8fHww', 1),
(69, 10, 'https://images.unsplash.com/photo-1763162410742-1d0097cea556?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8TGVub3ZvJTIwVGhpbmtQYWQlMjBYMSUyMENhcmJvbiUyMEdlbiUyMDEyfGVufDB8fDB8fHww', 0),
(70, 9, 'https://images.unsplash.com/photo-1727132527904-528bb0afb370?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTN8fE1pY3Jvc29mdCUyMFN1cmZhY2UlMjBMYXB0b3AlMjA1fGVufDB8fDB8fHww', 0),
(71, 9, 'https://images.unsplash.com/photo-1587613842578-f811e043c1be?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTF8fE1pY3Jvc29mdCUyMFN1cmZhY2UlMjBMYXB0b3AlMjA1fGVufDB8fDB8fHww', 1),
(72, 9, 'https://images.unsplash.com/photo-1643900074574-8295e3f0af5e?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8TWljcm9zb2Z0JTIwU3VyZmFjZSUyMExhcHRvcCUyMDV8ZW58MHx8MHx8fDA%3D', 2),
(73, 8, 'https://images.unsplash.com/photo-1771015310937-6754da25e49a?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTN8fEFTVVMlMjBST0clMjBaZXBoeXJ1cyUyMEcxNHxlbnwwfHwwfHx8MA%3D%3D', 0),
(74, 8, 'https://images.unsplash.com/photo-1649269234296-f5130117f4ab?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8QVNVUyUyMFJPRyUyMFplcGh5cnVzJTIwRzE0fGVufDB8fDB8fHww', 1),
(75, 7, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fERlbGwlMjBYUFMlMjAxNXxlbnwwfHwwfHx8MA%3D%3D', 0),
(76, 25, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-blacktitanium?wid=800&hei=800&fmt=jpeg&qlt=90', 0),
(77, 25, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-whitetitanium?wid=800&hei=800&fmt=jpeg&qlt=90', 1);

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reviews`
--

INSERT INTO `reviews` (`review_id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(2, 2, 3, 5, 'nice product', '2026-05-10 07:50:21');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` bigint(20) UNSIGNED DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('client','admin') DEFAULT 'client',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verify_code` varchar(10) DEFAULT NULL,
  `verify_expires` datetime DEFAULT NULL,
  `google_id` varchar(200) DEFAULT NULL,
  `facebook_id` varchar(200) DEFAULT NULL,
  `oauth_provider` varchar(20) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(10) DEFAULT NULL,
  `code_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone`, `address`, `role`, `created_at`, `is_verified`, `verify_code`, `verify_expires`, `google_id`, `facebook_id`, `oauth_provider`, `email_verified`, `verification_code`, `code_expiry`) VALUES
(1, 'Admin', 'admin@techshop.com', '$2y$10$FbvdL2S84vBGdzl/yRjXduvF1V4RFnOZ.XPuzRsxJg7bt3UUcRcfm', 0, '', 'admin', '2026-05-04 23:35:54', 1, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(2, 'Jean Dupont', 'jean@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'client', '2026-05-04 23:35:54', 1, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(3, 'hocine', 'hocineguelaache@gmail.com', '$2y$10$FbvdL2S84vBGdzl/yRjXduvF1V4RFnOZ.XPuzRsxJg7bt3UUcRcfm', 778882104, 'akbou', 'admin', '2026-05-05 07:07:35', 1, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(4, 'gg', 'ehfbbw@gmail.com', '$2y$10$uuoGq202Z.zwpnv34lq/eOvpge61sHdqeR6VYtHZE.5cus4D13oFS', 778882104, 'ffffg', 'client', '2026-05-09 21:44:22', 0, '440934', '2026-05-09 23:59:22', NULL, NULL, NULL, 0, NULL, NULL),
(15, 'kamel', 'kamelboukhenak@gmail.com', '$2y$10$STwtEqpc2OB0LRmj79x/GOUd4EJENXsBTkTG5FFUHFiYbgvZDN0hK', 778882104, 'qwerty', 'client', '2026-05-10 18:51:34', 0, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(16, 'ggggggggggg', 'boukhenakkamel@gmail.com', '$2y$10$O3biVOyR.DY8/dKGvzjlWOj.yBfF6y3a61JXo77kz4d9Q3NipD79W', 778882104, 'azertyuio', 'client', '2026-05-10 18:53:58', 0, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(17, 'LYDIA', 'lydiabenmansour48@gmail.com', '$2y$10$/h1WkY5ZZ8hldunNeFeMaetbwQixkg9/t2RxMNrT/kjNUWJVbws9O', 778882104, 'SIDI AICH', 'client', '2026-05-11 07:02:53', 0, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Index pour la table `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`color_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `product_colors`
--
ALTER TABLE `product_colors`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT pour la table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `product_colors`
--
ALTER TABLE `product_colors`
  ADD CONSTRAINT `product_colors_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
