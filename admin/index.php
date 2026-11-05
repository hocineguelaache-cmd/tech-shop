<?php

require_once __DIR__ . '/../config.php';


session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}



$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_users    = $pdo->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn();
$revenue        = $pdo->query("SELECT SUM(total) FROM orders WHERE status != 'cancelled'")->fetchColumn();
if (!$revenue) {
    $revenue = 0;
}


$recent_orders = $pdo->query("SELECT o.*, u.name AS uname FROM orders o LEFT JOIN users u ON o.user_id=u.user_id ORDER BY o.created_at DESC LIMIT 10")->fetchAll();


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
    <title>Dashboard Admin - TECH-SHOP</title>
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
            .stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 480px) {
            .admin-sidebar a span {
                display: none;
            }
            .admin-sidebar a i {
                margin: 0;
            }
            .stat-grid {
                grid-template-columns: 1fr;
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
            <a href="products.php">Produits</a>
            <a href="orders.php">Commandes</a>
        </div>
        <div class="nav-actions">
            <button class="hamburger" id="hamburger" onclick="toggleMenu()">
                <i class="bx bx-menu"></i>
            </button>

            <a href="../toggle_theme.php" class="theme-toggle">
                <i class="bx <?php echo ($theme === 'dark') ? 'bx-sun' : 'bx-moon'; ?>"></i>
            </a>
            <?php if (isset($_SESSION['user_id'])) { ?>
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
        <a href="index.php">Dashboard</a>
        <a href="products.php">Produits</a>
        <a href="categories.php">Catégories</a>
        <a href="orders.php">Commandes</a>
        <a href="users.php">Utilisateurs</a>
        <a href="../index.php">Voir la boutique</a>
        <a href="../logout.php">Déconnexion</a>
    </div>
</nav>

<main class="main-content">
    <div class="admin-layout">
        <nav class="admin-sidebar">
            <a href="index.php" class="active"><i class="bx bx-grid-alt"></i> Dashboard</a>
            <a href="products.php"><i class="bx bx-package"></i> Produits</a>
            <a href="categories.php"><i class="bx bx-category"></i> Catégories</a>
            <a href="orders.php"><i class="bx bx-receipt"></i> Commandes</a>
            <a href="users.php"><i class="bx bx-group"></i> Utilisateurs</a>
            <a href="../index.php"><i class="bx bx-store"></i> Voir la boutique</a>
            <a href="../logout.php"><i class="bx bx-log-out"></i> Déconnexion</a>
        </nav>

        <div class="admin-main">
            <h2 class="section-title">Dashboard</h2>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="icon"><i class="bx bx-package"></i></div>
                    <div class="label">Produits</div>
                    <div class="value"><?php echo $total_products; ?></div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="bx bx-receipt"></i></div>
                    <div class="label">Commandes</div>
                    <div class="value"><?php echo $total_orders; ?></div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="bx bx-group"></i></div>
                    <div class="label">Clients</div>
                    <div class="value"><?php echo $total_users; ?></div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="bx bx-euro"></i></div>
                    <div class="label">Revenus</div>
                    <div class="value"><?php echo number_format($revenue, 0, ',', ' '); ?> DA</div>
                </div>
            </div>

            <h3 class="section-title">Commandes récentes</h3>
            <table class="table">
                <thead>
                    <tr><th>ID</th><th>Client</th><th>Total</th><th>Statut</th><th>Date</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $o) { ?>
                        <tr>
                            <td>#<?php echo $o['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($o['uname']); ?></td>
                            <td><?php echo number_format($o['total'], 2, ',', ' '); ?> DA</td>
                            <td><span class="status-badge status-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($o['created_at'])); ?></td>
                            <td><a href="orders.php?id=<?php echo $o['order_id']; ?>" class="btn btn-ghost" style="padding:6px 14px;font-size:.8rem">Voir</a></td>
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
            <a href="../login.php" style="color:var(--text-muted); text-decoration:none">Espace Admin</a>
        </div>
    </div>
</footer>

<script src="../assets/js/main.js"></script>
</body>
</html>
