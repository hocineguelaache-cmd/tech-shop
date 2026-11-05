<?php

require_once __DIR__ . '/config.php';
session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header('Location: login.php');
    exit;
}


$cart = array();
if (isset($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
}
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}


$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['qty'];
}


$user = $pdo->prepare("SELECT * FROM users WHERE user_id=?");
$user->execute([$_SESSION['user_id']]);
$u = $user->fetch();

$msg = '';
$err = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $addr  = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    if ($addr == '' || $phone == '') {
        $err = 'Adresse et téléphone obligatoires.';
    } else {
        $pdo->beginTransaction();
        try {
            $pdo->prepare("INSERT INTO orders (user_id,total,status) VALUES (?,?,'pending')")->execute([$_SESSION['user_id'], $total]);
            $order_id = $pdo->lastInsertId();

            foreach ($cart as $item) {
                $pdo->prepare("INSERT INTO order_items (order_id,product_id,quantity,price) VALUES (?,?,?,?)")->execute([$order_id, $item['product_id'], $item['qty'], $item['price']]);
            }

            $pdo->commit();
            unset($_SESSION['cart']);
            header("Location: profile.php?order=success");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $err = 'Erreur lors de la commande. Réessayez.';
        }
    }
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
    <title>Checkout - TECH-SHOP</title>
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
    <h2 class="section-title">Finaliser la commande</h2>

    <?php if ($err != '') { ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
    <?php } ?>

    <div class="cart-layout">
        <div class="card">
            <h3 style="margin-bottom:20px;font-weight:900">Informations de livraison</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Adresse de livraison *</label>
                    <textarea name="address" rows="3" class="large-input" required><?php echo htmlspecialchars($u['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Téléphone *</label>
                    <input type="text" name="phone" class="large-input" value="<?php echo htmlspecialchars($u['phone'] ?? ''); ?>" required>
                </div>
                <div class="alert alert-success" style="margin-top:20px">
                    <i class="bx bx-money"></i> Paiement à la livraison uniquement
                </div>
                <button type="submit" class="btn btn-primary btn-full" style="margin-top:16px">Confirmer la commande (<?php echo number_format($total, 2, ',', ' '); ?> DA)</button>
            </form>
        </div>

        <aside>
            <div class="order-summary">
                <h3 style="margin-bottom:20px;font-weight:900">Votre commande</h3>
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
            </div>
        </aside>
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
</body>
</html>
