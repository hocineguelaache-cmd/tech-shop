<?php

require_once __DIR__ . '/config.php';
session_start();


$id = 0;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}

$product = $pdo->prepare("SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.category_id WHERE p.product_id=?");
$product->execute([$id]);
$p = $product->fetch();


$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['qty'];
    }
}


if (!$p) {
    ?>
    <!DOCTYPE html>
    <?php $theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark'; ?>
<html lang="fr" data-theme="<?php echo htmlspecialchars($theme); ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>TECH-SHOP</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php"><img class="nav-logo-img" src="logo.png" alt="TECH-SHOP"></a>
        </div>
    </nav>
    <main class="main-content">
        <p>Produit introuvable.</p>
    </main>
    <footer class="footer">
        <div class="footer-inner">
            <img class="nav-logo-img" src="logo.png" alt="TECH-SHOP" style="height:32px;">
            <p>&copy; <?php echo date('Y'); ?> TECH-SHOP. Tous droits réservés.</p>
        </div>
    </footer>
    </body>
    </html>
    <?php
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $qty = (int)$_POST['qty'];
    if ($qty < 1) $qty = 1;

    $chosen_color_name = isset($_POST['color_name']) ? trim($_POST['color_name']) : '';
    $chosen_color_hex  = isset($_POST['color_hex'])  ? trim($_POST['color_hex'])  : '';

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = array();

    $cart_key = $id;
    if ($chosen_color_name !== '') $cart_key = $id . '_' . md5($chosen_color_name);

    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['qty'] += $qty;
    } else {
        $_SESSION['cart'][$cart_key] = array(
            'product_id' => $id,
            'name'       => $p['name'],
            'price'      => $p['price'],
            'image'      => $p['image'],
            'brand'      => $p['brand'],
            'qty'        => $qty,
            'color_name' => $chosen_color_name,
            'color_hex'  => $chosen_color_hex
        );
    }
    header('Location: cart.php');
    exit;
}


$imgs_stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id=? ORDER BY sort_order ASC");
$imgs_stmt->execute([$id]);
$product_images = $imgs_stmt->fetchAll(PDO::FETCH_COLUMN);
if (empty($product_images) && $p['image']) $product_images = [$p['image']];


$colors_stmt = $pdo->prepare("SELECT color_id, color_name, color_hex FROM product_colors WHERE product_id=? ORDER BY sort_order ASC");
$colors_stmt->execute([$id]);
$product_colors = $colors_stmt->fetchAll();


$reviews = $pdo->prepare("SELECT r.*, u.name AS uname FROM reviews r LEFT JOIN users u ON r.user_id=u.user_id WHERE r.product_id=? ORDER BY r.created_at DESC");
$reviews->execute([$id]);
$revs = $reviews->fetchAll();


$avg = $pdo->prepare("SELECT AVG(rating) FROM reviews WHERE product_id=?");
$avg->execute([$id]);
$avg_rating = round((float)$avg->fetchColumn(), 1);

$msg = '';
$err = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    if (!isset($_SESSION['user_id'])) {
        $err = 'Connectez-vous pour laisser un avis.';
    } else {
        $rating  = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        if ($rating < 1 || $rating > 5) {
            $err = 'Note invalide.';
        } else {
            $pdo->prepare("INSERT INTO reviews (product_id,user_id,rating,comment) VALUES (?,?,?,?)")->execute([$id, $_SESSION['user_id'], $rating, $comment]);
            header("Location: product.php?id=$id");
            exit;
        }
    }
}

$stars_full  = (int)$avg_rating;
$stars_empty = 5 - $stars_full;
?>
<!DOCTYPE html>
<?php $theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark'; ?>
<html lang="fr" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($p['name']); ?> — TECH-SHOP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .gallery-wrap { display: flex; flex-direction: column; gap: 12px; }
        .gallery-main { width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 24px; border: 1px solid var(--border-color); background: var(--bg-surface); transition: opacity .25s; }
        .gallery-thumbs { display: flex; gap: 8px; flex-wrap: wrap; }
        .gallery-thumbs img { width: 68px; height: 68px; object-fit: cover; border-radius: 12px; border: 2px solid transparent; cursor: pointer; opacity: .65; transition: all .2s; }
        .gallery-thumbs img.active, .gallery-thumbs img:hover { border-color: var(--accent); opacity: 1; }
    </style>
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
    <div class="product-detail">
        <div class="gallery-wrap">
            <?php $first_img = !empty($product_images) ? $product_images[0] : ''; ?>
            <img id="gallery-main-img" class="gallery-main" src="<?php echo htmlspecialchars($first_img); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
            <?php if (count($product_images) > 1) { ?>
            <div class="gallery-thumbs">
                <?php foreach ($product_images as $i => $img_url) { ?>
                    <img src="<?php echo htmlspecialchars($img_url); ?>" alt="Image <?php echo $i+1; ?>" class="<?php echo $i === 0 ? 'active' : ''; ?>" onclick="switchGalleryImg(this, '<?php echo htmlspecialchars($img_url, ENT_QUOTES); ?>')">
                <?php } ?>
            </div>
            <?php } ?>
        </div>
        <div class="product-info">
            <p class="brand"><?php echo htmlspecialchars($p['brand']); ?> · <?php echo htmlspecialchars($p['cat_name']); ?></p>
            <h1><?php echo htmlspecialchars($p['name']); ?></h1>
            <?php if ($avg_rating > 0) { ?>
                <div class="stars">
                    <?php echo str_repeat('★', $stars_full) . str_repeat('☆', $stars_empty); ?>
                    <small style="color:var(--text-muted)">(<?php echo count($revs); ?> avis)</small>
                </div>
            <?php } ?>
            <div class="price"><?php echo number_format($p['price'], 2, ',', ' '); ?> DA</div>
            <?php if ($p['stock'] > 0) { ?>
                <span class="stock-badge in">En stock (<?php echo $p['stock']; ?>)</span>
            <?php } else { ?>
                <span class="stock-badge out">Rupture de stock</span>
            <?php } ?>
            <p class="product-desc"><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>

            <?php if (!empty($product_colors)) { ?>
                <div class="color-section">
                    <span class="color-section-label">Couleur</span>
                    <div class="color-chips" id="color-chips-wrap">
                        <?php foreach ($product_colors as $ci => $col) { ?>
                            <button type="button" class="color-chip <?php echo $ci === 0 ? 'selected' : ''; ?>" style="background-color: <?php echo htmlspecialchars($col['color_hex']); ?>;" data-name="<?php echo htmlspecialchars($col['color_name']); ?>" data-hex="<?php echo htmlspecialchars($col['color_hex']); ?>" onclick="selectColor(this)" title="<?php echo htmlspecialchars($col['color_name']); ?>">
                                <span class="color-chip-tooltip"><?php echo htmlspecialchars($col['color_name']); ?></span>
                            </button>
                        <?php } ?>
                        <span class="color-selected-label" id="color-selected-text"><?php echo htmlspecialchars($product_colors[0]['color_name']); ?></span>
                    </div>
                </div>
            <?php } ?>

            <?php if ($p['size'] != '') { ?>
                <p style="color:var(--text-muted);font-size:.85rem;margin-bottom:12px">Taille: <?php echo htmlspecialchars($p['size']); ?></p>
            <?php } ?>

            <?php if ($p['stock'] > 0) { ?>
                <form method="POST" id="add-cart-form">
                    <input type="hidden" name="color_name" id="chosen-color-name" value="<?php echo !empty($product_colors) ? htmlspecialchars($product_colors[0]['color_name']) : ''; ?>">
                    <input type="hidden" name="color_hex"  id="chosen-color-hex"  value="<?php echo !empty($product_colors) ? htmlspecialchars($product_colors[0]['color_hex']) : ''; ?>">
                    <div class="qty-wrap">
                        <button type="button" class="qty-btn" data-action="dec">−</button>
                        <input type="number" name="qty" value="1" min="1" max="<?php echo $p['stock']; ?>" class="large-input qty-input" style="width:80px;text-align:center">
                        <button type="button" class="qty-btn" data-action="inc">+</button>
                    </div>
                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-full"><i class="bx bx-cart-add"></i> Ajouter au panier</button>
                </form>
            <?php } ?>
        </div>
    </div>

    <div style="margin-top:48px">
        <h2 class="section-title">Avis clients</h2>
        <?php if ($err != '') echo '<div class="alert alert-error">'.htmlspecialchars($err).'</div>'; ?>
        <?php if (isset($_SESSION['user_id'])) { ?>
            <div class="card" style="max-width:500px;margin-bottom:24px">
                <p style="font-weight:700;margin-bottom:16px">Laisser un avis</p>
                <form method="POST">
                    <div class="form-group"><label>Note (1-5)</label><input type="number" name="rating" min="1" max="5" class="large-input" required></div>
                    <div class="form-group"><label>Commentaire</label><textarea name="comment" rows="3" class="large-input"></textarea></div>
                    <button type="submit" name="add_review" class="btn btn-primary">Publier</button>
                </form>
            </div>
        <?php } ?>
        <?php foreach ($revs as $r) { ?>
            <div class="card" style="margin-bottom:12px">
                <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                    <strong><?php echo htmlspecialchars($r['uname']); ?></strong>
                    <span class="stars"><?php echo str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']); ?></span>
                </div>
                <p style="color:var(--text-muted);font-size:.9rem"><?php echo nl2br(htmlspecialchars($r['comment'])); ?></p>
            </div>
        <?php } ?>
        <?php if (empty($revs)) echo '<p style="color:var(--text-muted)">Aucun avis pour l\'instant.</p>'; ?>
    </div>
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
<script>
function switchGalleryImg(thumb, url) {
    var main = document.getElementById('gallery-main-img');
    main.style.opacity = '0';
    setTimeout(function() { main.src = url; main.style.opacity = '1'; }, 150);
    document.querySelectorAll('.gallery-thumbs img').forEach(function(t) { t.classList.remove('active'); });
    thumb.classList.add('active');
}
function selectColor(chip) {
    document.querySelectorAll('#color-chips-wrap .color-chip').forEach(function(c) { c.classList.remove('selected'); });
    chip.classList.add('selected');
    var name = chip.getAttribute('data-name');
    var hex  = chip.getAttribute('data-hex');
    var label = document.getElementById('color-selected-text');
    if (label) label.textContent = name;
    var inputName = document.getElementById('chosen-color-name');
    var inputHex  = document.getElementById('chosen-color-hex');
    if (inputName) inputName.value = name;
    if (inputHex)  inputHex.value  = hex;
}
</script>
</body>
</html>
