<?php

require_once __DIR__ . '/../config.php';


session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$msg = '';
$err = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   
    if (isset($_POST['delete'])) {
        $cat_id = (int)$_POST['delete'];
       
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $stmt->execute([$cat_id]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $err = "Impossible de supprimer la catégorie : elle contient $count produit(s).";
        } else {
            $pdo->prepare("DELETE FROM categories WHERE category_id = ?")->execute([$cat_id]);
            $msg = 'Catégorie supprimée.';
        }
    }
   
    else if (isset($_POST['save'])) {
        $name = trim($_POST['name']);
        if (empty($name)) {
            $err = 'Le nom de la catégorie est obligatoire.';
        } else {
            if (isset($_POST['category_id']) && $_POST['category_id'] > 0) {
             
                $pdo->prepare("UPDATE categories SET name = ? WHERE category_id = ?")->execute([$name, (int)$_POST['category_id']]);
                $msg = 'Catégorie mise à jour.';
            } else {
                
                $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$name]);
                $msg = 'Catégorie créée.';
            }
        }
    }
}


$cats = $pdo->query("SELECT c.*, COUNT(p.product_id) AS pcount 
                     FROM categories c 
                     LEFT JOIN products p ON c.category_id = p.category_id 
                     GROUP BY c.category_id 
                     ORDER BY c.category_id DESC")->fetchAll();


$edit = null;
if (isset($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
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
    <title>Gestion des catégories - Admin TECH-SHOP</title>
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
            <a href="categories.php" class="active"><i class="bx bx-category"></i> Catégories</a>
            <a href="orders.php"><i class="bx bx-receipt"></i> Commandes</a>
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

            <div style="display:grid;grid-template-columns:1fr 340px;gap:32px;align-items:start">
                
                <div>
                    <h2 class="section-title">Catégories</h2>
                    <table class="table">
                        <thead>
                            <tr><th>ID</th><th>Nom</th><th>Produits</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cats as $c) { ?>
                                <tr>
                                    <td><?php echo $c['category_id']; ?></td>
                                    <td><?php echo htmlspecialchars($c['name']); ?></td>
                                    <td><?php echo $c['pcount']; ?></td>
                                    <td style="display:flex;gap:8px">
                                        <a href="?edit=<?php echo $c['category_id']; ?>" class="btn btn-ghost" style="padding:6px 12px;font-size:.8rem">Éditer</a>
                                        <form method="POST" onsubmit="return confirm('Supprimer cette catégorie ?')">
                                            <button type="submit" name="delete" value="<?php echo $c['category_id']; ?>" class="btn btn-danger" style="padding:6px 12px;font-size:.8rem">Suppr.</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:20px;font-weight:900">
                        <?php if ($edit) echo 'Modifier'; else echo 'Nouvelle'; ?> catégorie
                    </h3>
                    <form method="POST">
                        <?php if ($edit) { ?>
                            <input type="hidden" name="category_id" value="<?php echo $edit['category_id']; ?>">
                        <?php } ?>
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" name="name" class="large-input" value="<?php if ($edit) echo htmlspecialchars($edit['name']); ?>" required>
                        </div>
                        <button type="submit" name="save" class="btn btn-primary btn-full">
                            <?php if ($edit) echo 'Mettre à jour'; else echo 'Créer'; ?>
                        </button>
                        <?php if ($edit) { ?>
                            <a href="categories.php" class="btn btn-ghost btn-full" style="margin-top:8px">Annuler</a>
                        <?php } ?>
                    </form>
                </div>
            </div>
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
