<?php

require_once __DIR__ . '/../config.php';


session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_role'])) {
    $uid = (int)$_POST['user_id'];
    $cur = $pdo->prepare("SELECT role FROM users WHERE user_id=?");
    $cur->execute([$uid]);
    $r = $cur->fetchColumn();
    $new = ($r === 'admin') ? 'client' : 'admin';
    $pdo->prepare("UPDATE users SET role=? WHERE user_id=?")->execute([$new, $uid]);
    $msg = "Rôle mis à jour.";
}

$users = $pdo->query("SELECT u.*, COUNT(o.order_id) AS order_count FROM users u LEFT JOIN orders o ON u.user_id=o.user_id GROUP BY u.user_id ORDER BY u.created_at DESC")->fetchAll();

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
    <title>Gestion des utilisateurs - Admin TECH-SHOP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        @media (max-width: 768px) {
            .admin-layout {
                display: block;
            }
            .admin-sidebar {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
                padding: 10px;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                position: static;
                height: auto;
            }
            .admin-sidebar a {
                padding: 8px 12px;
                font-size: 0.8rem;
                border-radius: 8px;
            }
            .admin-main {
                padding: 16px;
                overflow-x: auto;
            }
            .table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            .table th, .table td {
                padding: 8px 10px;
            }
            .status-badge {
                font-size: 0.7rem;
                padding: 2px 8px;
            }
            .btn-ghost {
                padding: 4px 8px !important;
                font-size: 0.7rem !important;
            }
        }
        @media (max-width: 480px) {
            .admin-sidebar a span {
                display: none;
            }
            .admin-sidebar a i {
                margin: 0;
            }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="../index.php"><img class="nav-logo-img" src="../logo.png" alt="TECH-SHOP"></a>
        <div class="nav-links">
            <a href="../catalog.php">Catalogue</a>
            <a href="../catalog.php?cat=1">Téléphones</a>
            <a href="../catalog.php?cat=2">Ordinateurs</a>
            <a href="../catalog.php?cat=3">Casques</a>
            <a href="../catalog.php?cat=4">Montres</a>
            <a href="../catalog.php?cat=5">Accessoires</a>
        </div>
        <div class="nav-actions">
            <button class="hamburger" id="hamburger" onclick="toggleMenu()">
                <i class="bx bx-menu"></i>
            </button>

            <a href="../toggle_theme.php" class="theme-toggle">
                <i class="bx <?php echo ($theme === 'dark') ? 'bx-sun' : 'bx-moon'; ?>"></i>
            </a>
            <a href="../cart.php">
                <i class="bx bx-cart"></i>
                <?php if ($cart_count > 0) { ?>
                    <span class="badge"><?php echo $cart_count; ?></span>
                <?php } ?>
            </a>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <a href="../profile.php"><i class="bx bx-user"></i></a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
                    <a href="index.php"><i class="bx bx-shield"></i></a>
                <?php } ?>
                <a href="../logout.php"><i class="bx bx-log-out"></i></a>
            <?php } else { ?>
                <a href="../login.php"><i class="bx bx-log-in"></i></a>
            <?php } ?>
        </div>
    </div>
    <div class="mobile-menu" id="mobile-menu">
        <a href="../catalog.php">Catalogue</a>
        <a href="../catalog.php?cat=1">Téléphones</a>
        <a href="../catalog.php?cat=2">Ordinateurs</a>
        <a href="../catalog.php?cat=3">Casques</a>
        <a href="../catalog.php?cat=4">Montres</a>
        <a href="../catalog.php?cat=5">Accessoires</a>
        <a href="../cart.php">Panier</a>
        <?php if (isset($_SESSION['user_id'])) { ?>
            <a href="../profile.php">Mon compte</a>
            <a href="../logout.php">Déconnexion</a>
        <?php } else { ?>
            <a href="../login.php">Connexion</a>
        <?php } ?>
    </div>
</nav>

<main class="main-content">
    <div class="admin-layout">
        <nav class="admin-sidebar">
            <a href="index.php"><i class="bx bx-grid-alt"></i> Dashboard</a>
            <a href="products.php"><i class="bx bx-package"></i> Produits</a>
            <a href="categories.php"><i class="bx bx-category"></i> Catégories</a>
            <a href="orders.php"><i class="bx bx-receipt"></i> Commandes</a>
            <a href="users.php" class="active"><i class="bx bx-group"></i> Utilisateurs</a>
            <a href="../index.php"><i class="bx bx-store"></i> Voir la boutique</a>
            <a href="../logout.php"><i class="bx bx-log-out"></i> Déconnexion</a>
        </nav>

        <div class="admin-main">
            <?php if ($msg != '') { ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
            <?php } ?>

            <h2 class="section-title">Utilisateurs (<?php echo count($users); ?>)</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Rôle</th><th>Commandes</th><th>Inscrit le</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u) { ?>
                        <tr>
                            <td><?php echo $u['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($u['name']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo htmlspecialchars($u['phone'] ?? ''); ?></td>
                            <td>
                                <?php if ($u['role'] === 'admin') { ?>
                                    <span class="status-badge status-processing"><?php echo ucfirst($u['role']); ?></span>
                                <?php } else { ?>
                                    <span class="status-badge status-shipped"><?php echo ucfirst($u['role']); ?></span>
                                <?php } ?>
                            </td>
                            <td><?php echo $u['order_count']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                            <td>
                                <?php if ($u['user_id'] != $_SESSION['user_id']) { ?>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                        <button type="submit" name="toggle_role" class="btn btn-ghost" style="padding:6px 12px;font-size:.8rem">
                                            <?php if ($u['role'] === 'admin') echo '→ Client'; else echo '→ Admin'; ?>
                                        </button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="footer-inner">
        <img class="nav-logo-img" src="../logo.png" alt="TECH-SHOP" style="height:32px;">
        <div style="display:flex; gap:24px; align-items:center">
            <p>&copy; <?php echo date('Y'); ?> TECH-SHOP. Tous droits réservés.</p>
       
        </div>
    </div>
</footer>

<script src="../assets/js/main.js"></script>
</body>
</html>
