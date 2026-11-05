<?php

require_once __DIR__ . '/config.php';


session_start();


if (isset($_POST['remove'])) {
    unset($_SESSION['cart'][(int)$_POST['remove']]);
    header('Location: cart.php');
    exit;
}


if (isset($_POST['update'])) {
    foreach ($_POST['qty'] as $pid => $qty) {
        $qty = (int)$qty;
        if ($qty < 1) {
            $qty = 1;
        }
        if (isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid]['qty'] = $qty;
        }
    }
    header('Location: cart.php');
    exit;
}


$cart = array();
if (isset($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
}


$total = 0;
foreach ($cart as $item) {
    $total += ($item['price'] * $item['qty']);
}


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
    <title>Panier - TECH-SHOP</title>
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
            <a href="../toggle_theme.php" class="theme-toggle">
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
    <h2 class="section-title">Mon Panier</h2>

    <?php if (empty($cart)) { ?>
        <p style="color:var(--text-muted)">Votre panier est vide. <a href="catalog.php" style="color:var(--accent)">Continuer vos achats</a></p>
    <?php } else { ?>

    <div class="cart-layout">
        <div>
            <!-- Formulaire pour mettre à jour les quantités -->
            <form method="POST">
                <?php foreach ($cart as $pid => $item) { ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="">
                        <div class="cart-item-info">
                            <p class="name"><?php echo htmlspecialchars($item['name']); ?></p>
                            <?php if (!empty($item['color_name'])) { ?>
                                <div class="cart-item-color">
                                    <span class="color-dot" style="background-color: <?php echo htmlspecialchars($item['color_hex']); ?>;"></span>
                                    <?php echo htmlspecialchars($item['color_name']); ?>
                                </div>
                            <?php } ?>
                            <p class="price"><?php echo number_format($item['price'], 2, ',', ' '); ?> DA</p>
                        </div>
                        <input type="number" name="qty[<?php echo $pid; ?>]" value="<?php echo $item['qty']; ?>" min="1" class="large-input" style="width:70px;text-align:center">
                    </div>
                <?php } ?>
                <div style="margin-top:16px;display:flex;gap:12px">
                    <button type="submit" name="update" class="btn btn-ghost">Mettre à jour</button>
                </div>
            </form>

           
            <form method="POST" style="margin-top:8px">
                <?php foreach ($cart as $pid => $item) { ?>
                    <button type="submit" name="remove" value="<?php echo $pid; ?>" class="btn btn-danger" style="margin-bottom:8px;font-size:.8rem;padding:8px 16px">Retirer <?php echo htmlspecialchars($item['name']); ?></button><br>
                <?php } ?>
            </form>
        </div>

        <aside>
            <div class="order-summary">
                <h3 style="margin-bottom:20px;font-weight:900">Récapitulatif</h3>
                <?php foreach ($cart as $item) { ?>
                    <div class="summary-row">
                        <span><?php echo htmlspecialchars($item['name']); ?> ×<?php echo $item['qty']; ?></span>
                        <span><?php echo number_format($item['price'] * $item['qty'], 2, ',', ' '); ?> DA</span>
                    </div>
                <?php } ?>
                <div class="summary-row total">
                    <span>Total</span>
                    <span><?php echo number_format($total, 2, ',', ' '); ?> DA</span>
                </div>

                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'client') { ?>
                    <a href="checkout.php" class="btn btn-primary btn-full" style="margin-top:20px">Passer la commande</a>
                <?php } else { ?>
                    <a href="login.php" class="btn btn-primary btn-full" style="margin-top:20px;background:var(--warning)">Connectez-vous pour commander</a>
                <?php } ?>
            </div>
        </aside>
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
