<?php

require_once __DIR__ . '/../config.php';


session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}


$msg = '';
$err = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {

    $order_id   = (int)$_POST['order_id'];
    $new_status = trim($_POST['status']);


    $current = $pdo->prepare("SELECT status FROM orders WHERE order_id=?");
    $current->execute([$order_id]);
    $old_status = $current->fetchColumn();

    if ($old_status !== $new_status) {

        $pdo->beginTransaction();

        try {

            if ($old_status === 'pending' && $new_status === 'processing') {

                $items = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id=?");
                $items->execute([$order_id]);

                foreach ($items->fetchAll() as $item) {

                    $stock = $pdo->prepare("SELECT stock FROM products WHERE product_id=? FOR UPDATE");
                    $stock->execute([$item['product_id']]);
                    $current_stock = $stock->fetchColumn();

                    if ($current_stock < $item['quantity']) {
                        throw new Exception('Stock insuffisant pour le produit #' . $item['product_id']);
                    }


                    $pdo->prepare("UPDATE products SET stock=stock-? WHERE product_id=?")->execute([$item['quantity'], $item['product_id']]);
                }
            }

            else if ($old_status === 'processing' && $new_status === 'cancelled') {

                $items = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id=?");
                $items->execute([$order_id]);

                foreach ($items->fetchAll() as $item) {
                    $pdo->prepare("UPDATE products SET stock=stock+? WHERE product_id=?")->execute([$item['quantity'], $item['product_id']]);
                }
            }


            $pdo->prepare("UPDATE orders SET status=? WHERE order_id=?")->execute([$new_status, $order_id]);
            $pdo->commit();
            $msg = 'Statut mis à jour.';

        } catch (Exception $e) {
            $pdo->rollBack();
            $err = $e->getMessage();
        }
    }
}


$detail = null;
if (isset($_GET['id'])) {

    $s = $pdo->prepare("SELECT o.*, u.name AS uname, u.email, u.phone, u.address FROM orders o LEFT JOIN users u ON o.user_id=u.user_id WHERE o.order_id=?");
    $s->execute([(int)$_GET['id']]);
    $detail = $s->fetch();

    if ($detail) {
        $items = $pdo->prepare("SELECT oi.*, p.name AS pname, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id=p.product_id WHERE oi.order_id=?");
        $items->execute([$detail['order_id']]);
        $detail['items'] = $items->fetchAll();
    }
}


$orders = $pdo->query("SELECT o.*, u.name AS uname FROM orders o LEFT JOIN users u ON o.user_id=u.user_id ORDER BY o.created_at DESC")->fetchAll();


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
    <title>Commandes Admin — TECH-SHOP</title>
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
            div[style*="grid-template-columns"] {
                display: block !important;
            }
            .card {
                margin-top: 20px;
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
        <a href="../admin/index.php">Dashboard Admin</a>
        <a href="../admin/products.php">Produits</a>
        <a href="../admin/orders.php">Commandes</a>
        <a href="../logout.php">Déconnexion</a>
    </div>
</nav>

<main class="main-content">

<div class="admin-layout">

    <nav class="admin-sidebar">
        <a href="index.php"><i class="bx bx-grid-alt"></i> Dashboard</a>
        <a href="products.php"><i class="bx bx-package"></i> Produits</a>
        <a href="categories.php"><i class="bx bx-category"></i> Catégories</a>
        <a href="orders.php" class="active"><i class="bx bx-receipt"></i> Commandes</a>
        <a href="users.php"><i class="bx bx-group"></i> Utilisateurs</a>
        <a href="../index.php"><i class="bx bx-store"></i> Voir la boutique</a>
        <a href="../logout.php"><i class="bx bx-log-out"></i> Déconnexion</a>
    </nav>

    <div class="admin-main">

        <?php if ($msg != '') { ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
        <?php } ?>
        <?php if ($err != '') { ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
        <?php } ?>

        <?php if ($detail) { ?>


            <a href="orders.php" class="btn btn-ghost" style="margin-bottom:20px"><i class="bx bx-arrow-back"></i> Retour</a>
            <h2 class="section-title">Commande #<?php echo $detail['order_id']; ?></h2>

            <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">
                <div>
                    <div class="card" style="margin-bottom:16px">
                        <p><strong><?php echo htmlspecialchars($detail['uname']); ?></strong></p>
                        <p style="color:var(--text-muted)"><?php echo htmlspecialchars($detail['email']); ?> · <?php echo htmlspecialchars($detail['phone'] ?? ''); ?></p>
                        <p style="color:var(--text-muted);margin-top:8px"><?php echo htmlspecialchars($detail['address'] ?? ''); ?></p>
                    </div>

                    <table class="table">
                        <thead>
                            <tr><th>Produit</th><th>Qté</th><th>Prix unit.</th><th>Sous-total</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detail['items'] as $it) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($it['pname']); ?></td>
                                    <td><?php echo $it['quantity']; ?></td>
                                    <td><?php echo number_format($it['price'], 2, ',', ' '); ?> DA</td>
                                    <td><?php echo number_format($it['price'] * $it['quantity'], 2, ',', ' '); ?> DA</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="card">
                    <p style="font-size:1.5rem;font-weight:900;margin-bottom:16px"><?php echo number_format($detail['total'], 2, ',', ' '); ?> DA</p>
                    <p style="color:var(--text-muted);font-size:.85rem;margin-bottom:20px"><?php echo date('d/m/Y H:i', strtotime($detail['created_at'])); ?></p>

                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $detail['order_id']; ?>">
                        <div class="form-group">
                            <label>Statut</label>
                            <select name="status" class="large-input">
                                <?php
                                $statuses = array('pending', 'processing', 'shipped', 'delivered', 'cancelled');
                                foreach ($statuses as $s) { ?>
                                    <option value="<?php echo $s; ?>" <?php if ($detail['status'] == $s) echo 'selected'; ?>><?php echo ucfirst($s); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-full">Mettre à jour</button>
                    </form>
                </div>
            </div>

        <?php } else { ?>


            <h2 class="section-title">Commandes (<?php echo count($orders); ?>)</h2>
            <table class="table">
                <thead>
                    <tr><th>ID</th><th>Client</th><th>Total</th><th>Statut</th><th>Date</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o) { ?>
                        <tr>
                            <td>#<?php echo $o['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($o['uname']); ?></td>
                            <td><?php echo number_format($o['total'], 2, ',', ' '); ?> DA</td>
                            <td><span class="status-badge status-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                            <td><a href="?id=<?php echo $o['order_id']; ?>" class="btn btn-ghost" style="padding:6px 12px;font-size:.8rem">Voir</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </tr>

        <?php } ?>

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
