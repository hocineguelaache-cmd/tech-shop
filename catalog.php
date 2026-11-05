<?php

require_once __DIR__ . '/config.php';
session_start();


$cat_id = 0;
if (isset($_GET['cat'])) {
    $cat_id = (int)$_GET['cat'];
}

$brand = '';
if (isset($_GET['brand'])) {
    $brand = trim($_GET['brand']);
}

$price_max = 0;
if (isset($_GET['price_max'])) {
    $price_max = (float)$_GET['price_max'];
}

$sort = 'created_at';
if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
}

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}


$where = array('1=1');
$params = array();

if ($cat_id > 0) {
    $where[] = 'p.category_id = :cat';
    $params[':cat'] = $cat_id;
}
if ($brand != '') {
    $where[] = 'p.brand = :brand';
    $params[':brand'] = $brand;
}
if ($price_max > 0) {
    $where[] = 'p.price <= :price_max';
    $params[':price_max'] = $price_max;
}
if ($search != '') {
    $where[] = '(p.name LIKE :search OR p.brand LIKE :search2)';
    $params[':search']  = '%' . $search . '%';
    $params[':search2'] = '%' . $search . '%';
}


if ($sort === 'price_asc') {
    $order = 'p.price ASC';
} else if ($sort === 'price_desc') {
    $order = 'p.price DESC';
} else if ($sort === 'name') {
    $order = 'p.name ASC';
} else {
    $order = 'p.created_at DESC';
}


$sql = "SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE " . implode(' AND ', $where) . " ORDER BY $order";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();


$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$brands = $pdo->query("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' ORDER BY brand")->fetchAll(PDO::FETCH_COLUMN);


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
    <title>TECH-SHOP - Catalogue</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="index.php"><img class="nav-logo-img" src="logo.png" alt="TECH-SHOP"></a>
        <div class="nav-links">
            <a href="catalog.php">Catalogue</a>
            <a href="catalog.php?cat=1">Téléphones</a>
            <a href="catalog.php?cat=2">Ordinateurs</a>
            <a href="catalog.php?cat=3">Casques</a>
            <a href="catalog.php?cat=4">Montres</a>
            <a href="catalog.php?cat=5">Accessoires</a>
        </div>
        <div class="nav-actions">
            <button class="hamburger" id="hamburger" onclick="toggleMenu()">
                <i class="bx bx-menu"></i>
            </button>
            <a href="toggle_theme.php" class="theme-toggle">
                <i class="bx <?php echo ($theme === 'dark') ? 'bx-sun' : 'bx-moon'; ?>"></i>
            </a>
            <a href="cart.php">
                <i class="bx bx-cart"></i>
                <?php if ($cart_count > 0) { ?>
                    <span class="badge"><?php echo $cart_count; ?></span>
                <?php } ?>
            </a>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <a href="profile.php"><i class="bx bx-user"></i></a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
                    <a href="admin/index.php"><i class="bx bx-shield"></i></a>
                <?php } ?>
                <a href="logout.php"><i class="bx bx-log-out"></i></a>
            <?php } else { ?>
                <a href="login.php"><i class="bx bx-log-in"></i></a>
            <?php } ?>
        </div>
    </div>
    <div class="mobile-menu" id="mobile-menu">
        <a href="catalog.php">Catalogue</a>
        <a href="catalog.php?cat=1">Téléphones</a>
        <a href="catalog.php?cat=2">Ordinateurs</a>
        <a href="catalog.php?cat=3">Casques</a>
        <a href="catalog.php?cat=4">Montres</a>
        <a href="catalog.php?cat=5">Accessoires</a>
        <a href="cart.php">Panier</a>
        <?php if (isset($_SESSION['user_id'])) { ?>
            <a href="profile.php">Mon compte</a>
            <a href="logout.php">Déconnexion</a>
        <?php } else { ?>
            <a href="login.php">Connexion</a>
        <?php } ?>
    </div>
</nav>

<main class="main-content">


<div class="search-hero">
    <p class="search-hero-sub">Trouvez votre prochain appareil parmi nos produits</p>

    <form method="GET" id="search-form">
        <div class="search-hero-input-wrap">
            <i class="bx bx-search search-hero-icon"></i>
            <input type="text" name="search"
                   value="<?php echo htmlspecialchars($search); ?>"
                   placeholder="Rechercher un produit, une marque..."
                   class="search-hero-input" id="search-input" autocomplete="off">
            <?php if ($search != '') { ?>
                <a href="catalog.php<?php echo $cat_id > 0 ? '?cat='.$cat_id : ''; ?>"
                   class="search-hero-clear"><i class="bx bx-x"></i></a>
            <?php } ?>
            <button type="submit" class="search-hero-btn">
                <i class="bx bx-search"></i> <span>Rechercher</span>
            </button>
        </div>

        
        <div class="search-chips" style="margin-top:18px;">
            <button type="submit" name="cat" value="0"   class="chip <?php if ($cat_id === 0) echo 'chip-active'; ?>"><i class="bx bx-grid-alt"></i> Tous</button>
            <button type="submit" name="cat" value="1"   class="chip <?php if ($cat_id === 1) echo 'chip-active'; ?>"><i class="bx bx-mobile-alt"></i> Téléphones</button>
            <button type="submit" name="cat" value="2"   class="chip <?php if ($cat_id === 2) echo 'chip-active'; ?>"><i class="bx bx-laptop"></i> Ordinateurs</button>
            <button type="submit" name="cat" value="3"   class="chip <?php if ($cat_id === 3) echo 'chip-active'; ?>"><i class="bx bx-headphone"></i> Casques</button>
            <button type="submit" name="cat" value="4"   class="chip <?php if ($cat_id === 4) echo 'chip-active'; ?>"><i class="bx bx-watch"></i> Montres</button>
            <button type="submit" name="cat" value="5"   class="chip <?php if ($cat_id === 5) echo 'chip-active'; ?>"><i class="bx bx-plug"></i> Accessoires</button>
        </div>

        
        <div class="filter-row">
            <!-- Marque -->
            <div class="filter-select-wrap">
                <i class="bx bx-tag-alt filter-select-icon"></i>
                <select name="brand" class="filter-select">
                    <option value="">Toutes les marques</option>
                    <?php foreach ($brands as $b) { ?>
                        <option value="<?php echo htmlspecialchars($b); ?>" <?php if ($brand == $b) echo 'selected'; ?>><?php echo htmlspecialchars($b); ?></option>
                    <?php } ?>
                </select>
            </div>

        
            <div class="filter-select-wrap">
                <i class="bx bx-money filter-select-icon"></i>
                <input type="number" name="price_max" value="<?php echo $price_max > 0 ? $price_max : ''; ?>"
                       placeholder="Prix max (DA)" class="filter-select" min="0">
            </div>

            
            <div class="filter-select-wrap">
                <i class="bx bx-sort-alt-2 filter-select-icon"></i>
                <select name="sort" class="filter-select">
                    <option value="created_at" <?php if ($sort == 'created_at') echo 'selected'; ?>>Nouveautés</option>
                    <option value="price_asc"  <?php if ($sort == 'price_asc')  echo 'selected'; ?>>Prix croissant</option>
                    <option value="price_desc" <?php if ($sort == 'price_desc') echo 'selected'; ?>>Prix décroissant</option>
                    <option value="name"       <?php if ($sort == 'name')       echo 'selected'; ?>>Nom A-Z</option>
                </select>
            </div>

           

            <button type="submit" class="btn btn-primary" style="border-radius:12px; padding:11px 22px; white-space:nowrap">
                <i class="bx bx-filter-alt"></i> Filtrer
            </button>
        </div>
    </form>
</div>


<h2 class="section-title" style="margin-bottom:20px">
    <?php
        if ($search != '') {
            echo 'Résultats pour « ' . htmlspecialchars($search) . ' »';
        } elseif ($cat_id > 0) {
            foreach ($categories as $c) {
                if ($c['category_id'] == $cat_id) { echo htmlspecialchars($c['name']); }
            }
        } else {
            echo 'Tous les produits';
        }
        echo ' <span style="color:var(--text-muted);font-size:.9rem;font-weight:500">(' . count($products) . ' produit' . (count($products) > 1 ? 's' : '') . ')</span>';
    ?>
</h2>


<?php if (empty($products)) { ?>
    <div style="text-align:center; padding:60px 0; color:var(--text-muted)">
        <i class="bx bx-search-alt" style="font-size:3rem; display:block; margin-bottom:12px"></i>
        Aucun produit trouvé. Essayez un autre terme.
    </div>
<?php } else { ?>
    <div class="products-grid-full">
        <?php foreach ($products as $p) { ?>
            <a href="product.php?id=<?php echo $p['product_id']; ?>" class="card">
                <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                <span class="brand-tag"><?php echo htmlspecialchars($p['brand']); ?></span>
                <p class="card-name"><?php echo htmlspecialchars($p['name']); ?></p>
                <span class="card-price"><?php echo number_format($p['price'], 2, ',', ' '); ?> DA</span>
            </a>
        <?php } ?>
    </div>
<?php } ?>

</main>

<footer class="footer">
    <div class="footer-inner">
        <img class="nav-logo-img" src="logo.png" alt="TECH-SHOP" style="height:32px;">
        <div style="display:flex; gap:24px; align-items:center">
            <p>&copy; <?php echo date('Y'); ?> TECH-SHOP. Tous droits réservés.</p>
          
        </div>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
