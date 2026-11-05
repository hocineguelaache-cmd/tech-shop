<?php
require_once __DIR__ . '/config.php';
session_start();

$new_products = $pdo->query("SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.created_at DESC LIMIT 8")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['qty'];
    }
}
?>
<!DOCTYPE html>
<?php $theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark'; ?>
<html lang="fr" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TECH-SHOP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/tech-shop/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="/tech-shop/index.php"><img class="nav-logo-img" src="/tech-shop/logo.png" alt="TECH-SHOP"></a>
        <div class="nav-links">
            <a href="/tech-shop/catalog.php">Catalogue</a>
            <a href="/tech-shop/catalog.php?cat=1">Téléphones</a>
            <a href="/tech-shop/catalog.php?cat=2">Ordinateurs</a>
            <a href="/tech-shop/catalog.php?cat=3">Casques</a>
            <a href="/tech-shop/catalog.php?cat=4">Montres</a>
            <a href="/tech-shop/catalog.php?cat=5">Accessoires</a>
        </div>
        <div class="nav-actions">
            <button class="hamburger" id="hamburger" aria-label="Menu" onclick="toggleMenu()">
                <i class="bx bx-menu" id="hamburger-icon"></i>
            </button>
            <a href="/tech-shop/toggle_theme.php" class="theme-toggle">
                <i class="bx <?php echo ($theme === 'dark') ? 'bx-sun' : 'bx-moon'; ?>"></i>
            </a>
            <a href="/tech-shop/cart.php">
                <i class="bx bx-cart"></i>
                <?php if ($cart_count > 0) { ?>
                    <span class="badge"><?php echo $cart_count; ?></span>
                <?php } ?>
            </a>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <a href="/tech-shop/profile.php"><i class="bx bx-user"></i></a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
                    <a href="/tech-shop/admin/index.php"><i class="bx bx-shield"></i></a>
                <?php } ?>
                <a href="/tech-shop/logout.php"><i class="bx bx-log-out"></i></a>
            <?php } else { ?>
                <a href="/tech-shop/login.php"><i class="bx bx-log-in"></i></a>
            <?php } ?>
        </div>
    </div>
    <div class="mobile-menu" id="mobile-menu">
        <a href="/tech-shop/catalog.php">Catalogue</a>
        <a href="/tech-shop/catalog.php?cat=1">Téléphones</a>
        <a href="/tech-shop/catalog.php?cat=2">Ordinateurs</a>
        <a href="/tech-shop/catalog.php?cat=3">Casques</a>
        <a href="/tech-shop/catalog.php?cat=4">Montres</a>
        <a href="/tech-shop/catalog.php?cat=5">Accessoires</a>
        <a href="/tech-shop/cart.php">Panier</a>
        <?php if (isset($_SESSION['user_id'])) { ?>
            <a href="/tech-shop/profile.php">Mon compte</a>
            <a href="/tech-shop/logout.php">Déconnexion</a>
        <?php } else { ?>
            <a href="/tech-shop/login.php">Connexion</a>
        <?php } ?>
    </div>
</nav>

<main class="main-content">
    <section class="hero">
        <h1>Le meilleur de la <span>Tech</span><br>livré chez vous.</h1>
        <p>Des milliers de produits high-tech au meilleur prix.</p>
        <a href="/tech-shop/catalog.php" class="btn btn-primary">Explorer le catalogue</a>
    </section>

    <div class="filters">
        <?php foreach ($categories as $cat) { ?>
            <a href="/tech-shop/catalog.php?cat=<?php echo $cat['category_id']; ?>" class="filter-btn"><?php echo htmlspecialchars($cat['name']); ?></a>
        <?php } ?>
    </div>

    <h2 class="section-title">Nouveautés</h2>
    <div class="grid-2">
        <?php foreach ($new_products as $p) { ?>
            <a href="/tech-shop/product.php?id=<?php echo $p['product_id']; ?>" class="card">
                <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                <span class="brand-tag"><?php echo htmlspecialchars($p['brand']); ?></span>
                <p class="card-name"><?php echo htmlspecialchars($p['name']); ?></p>
                <span class="card-price"><?php echo number_format($p['price'], 2, ',', ' '); ?> DA</span>
            </a>
        <?php } ?>
    </div>
</main>

<footer class="footer">
    <div class="footer-inner">
        <img class="nav-logo-img" src="/tech-shop/logo.png" alt="TECH-SHOP" style="height:32px;">
        <div style="display:flex; gap:24px; align-items:center">
            <p>&copy; <?php echo date('Y'); ?> TECH-SHOP. Tous droits réservés.</p>
            <a href="/tech-shop/login.php" style="color:var(--text-muted); text-decoration:none">Espace Admin</a>
        </div>
    </div>
</footer>

<script src="/tech-shop/assets/js/main.js"></script>
</body>
</html>
