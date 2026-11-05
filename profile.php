<?php

require_once __DIR__ . '/config.php';


session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


$user = $pdo->prepare("SELECT * FROM users WHERE user_id=?");
$user->execute([$_SESSION['user_id']]);
$u = $user->fetch();


$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
$orders->execute([$_SESSION['user_id']]);
$ords = $orders->fetchAll();

$msg = '';
$err = '';


if (isset($_GET['order']) && $_GET['order'] === 'success') {
    $msg = 'Commande passée avec succès !';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $addr  = trim($_POST['address']);

    $pdo->prepare("UPDATE users SET name=?, phone=?, address=? WHERE user_id=?")->execute([$name, $phone, $addr, $_SESSION['user_id']]);
    $_SESSION['name'] = $name;
    $msg = 'Profil mis à jour.';

    $user2 = $pdo->prepare("SELECT * FROM users WHERE user_id=?");
    $user2->execute([$_SESSION['user_id']]);
    $u = $user2->fetch();

    if (isset($_POST['new_password']) && $_POST['new_password'] != '') {
        if (strlen($_POST['new_password']) < 6) {
            $err = 'Mot de passe trop court.';
        } else {
            $hash = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
            $pdo->prepare("UPDATE users SET password=? WHERE user_id=?")->execute([$hash, $_SESSION['user_id']]);
            $msg = 'Profil et mot de passe mis à jour.';
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
    <title>TECH-SHOP - Mon profil</title>
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
        <a href="profile.php">Mon compte</a>
        <a href="logout.php">Déconnexion</a>
    </div>
</nav>

<main class="main-content">
    <h2 class="section-title">Mon Profil</h2>

    <?php if ($msg != '') echo '<div class="alert alert-success">'.htmlspecialchars($msg).'</div>'; ?>
    <?php if ($err != '') echo '<div class="alert alert-error">'.htmlspecialchars($err).'</div>'; ?>

    <div class="profile-grid">
        <div class="card">
            <h3 style="margin-bottom:20px;font-weight:900">Modifier mon profil</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="name" class="large-input" value="<?php echo htmlspecialchars($u['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="phone" class="large-input" value="<?php echo htmlspecialchars($u['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Adresse</label>
                    <textarea name="address" rows="2" class="large-input"><?php echo htmlspecialchars($u['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Nouveau mot de passe (optionnel)</label>
                    <input type="password" name="new_password" class="large-input">
                </div>
                <button type="submit" class="btn btn-primary btn-full">Enregistrer</button>
            </form>
        </div>

        <div>
            <h3 class="section-title">Mes commandes</h3>
            <?php if (empty($ords)) { ?>
                <p style="color:var(--text-muted)">Aucune commande pour l'instant.</p>
            <?php } else { ?>
                <?php foreach ($ords as $ord) { ?>
                    <div class="card" style="margin-bottom:12px">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <div>
                                <p style="font-weight:700">Commande #<?php echo $ord['order_id']; ?></p>
                                <p style="color:var(--text-muted);font-size:.85rem"><?php echo date('d/m/Y H:i', strtotime($ord['created_at'])); ?></p>
                            </div>
                            <div style="text-align:right">
                                <span class="status-badge status-<?php echo $ord['status']; ?>"><?php echo ucfirst($ord['status']); ?></span>
                                <p style="font-weight:900;margin-top:6px"><?php echo number_format($ord['total'], 2, ',', ' '); ?> DA</p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
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
