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
        $pdo->prepare("DELETE FROM products WHERE product_id=?")->execute([(int)$_POST['delete']]);
        $msg = 'Produit supprimé.';
    }
    else if (isset($_POST['save'])) {

        $d = $_POST;

        $image_urls = [];
        if (!empty($d['images']) && is_array($d['images'])) {
            foreach ($d['images'] as $url) {
                $url = trim($url);
                if ($url !== '') {
                    $image_urls[] = $url;
                }
            }
        }

        $primary_image = !empty($image_urls) ? $image_urls[0] : '';

        $color_names = isset($d['color_names']) && is_array($d['color_names']) ? $d['color_names'] : [];
        $color_hexes = isset($d['color_hexes']) && is_array($d['color_hexes']) ? $d['color_hexes'] : [];
        $colors_to_save = [];
        foreach ($color_names as $ci => $cname) {
            $cname = trim($cname);
            if ($cname !== '') {
                $colors_to_save[] = [
                    'name' => $cname,
                    'hex'  => isset($color_hexes[$ci]) ? $color_hexes[$ci] : '#000000'
                ];
            }
        }

        if (isset($d['product_id']) && $d['product_id'] > 0) {
            $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, size=?, brand=?, image=? WHERE product_id=?")->execute([
                $d['category_id'], $d['name'], $d['description'], $d['price'],
                $d['stock'], $d['size'], $d['brand'], $primary_image,
                $d['product_id']
            ]);

            $pdo->prepare("DELETE FROM product_images WHERE product_id=?")->execute([$d['product_id']]);
            $ins = $pdo->prepare("INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?,?,?)");
            foreach ($image_urls as $i => $url) {
                $ins->execute([$d['product_id'], $url, $i]);
            }

            $pdo->prepare("DELETE FROM product_colors WHERE product_id=?")->execute([$d['product_id']]);
            $insc = $pdo->prepare("INSERT INTO product_colors (product_id, color_name, color_hex, sort_order) VALUES (?,?,?,?)");
            foreach ($colors_to_save as $ci => $col) {
                $insc->execute([$d['product_id'], $col['name'], $col['hex'], $ci]);
            }
            $msg = 'Produit mis à jour.';
        } else {
            $pdo->prepare("INSERT INTO products (category_id, name, description, price, stock, size, brand, image) VALUES (?,?,?,?,?,?,?,?)")->execute([
                $d['category_id'], $d['name'], $d['description'], $d['price'],
                $d['stock'], $d['size'], $d['brand'], $primary_image
            ]);
            $new_id = $pdo->lastInsertId();

            $ins = $pdo->prepare("INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?,?,?)");
            foreach ($image_urls as $i => $url) {
                $ins->execute([$new_id, $url, $i]);
            }

            $insc = $pdo->prepare("INSERT INTO product_colors (product_id, color_name, color_hex, sort_order) VALUES (?,?,?,?)");
            foreach ($colors_to_save as $ci => $col) {
                $insc->execute([$new_id, $col['name'], $col['hex'], $ci]);
            }
            $msg = 'Produit créé.';
        }
    }
}

$products   = $pdo->query("SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.category_id ORDER BY p.product_id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

$edit        = null;
$edit_images = [];
$edit_colors = [];
if (isset($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM products WHERE product_id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();

    if ($edit) {
        $si = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id=? ORDER BY sort_order ASC");
        $si->execute([$edit['product_id']]);
        $edit_images = $si->fetchAll(PDO::FETCH_COLUMN);

        $sc = $pdo->prepare("SELECT color_name, color_hex FROM product_colors WHERE product_id=? ORDER BY sort_order ASC");
        $sc->execute([$edit['product_id']]);
        $edit_colors = $sc->fetchAll();
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
    <title>Produits Admin — TECH-SHOP</title>
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
        .img-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }
        .img-row input {
            flex: 1;
            min-width: 120px;
        }
        .img-row .preview-thumb {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: var(--bg-surface);
            display: block;
            flex-shrink: 0;
        }
        .img-row .remove-img-btn {
            background: none;
            border: 1px solid var(--danger);
            color: var(--danger);
            border-radius: 8px;
            width: 34px;
            height: 34px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all .2s;
        }
        .img-row .remove-img-btn:hover {
            background: var(--danger);
            color: #fff;
        }
        .add-img-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: 1px dashed var(--border-color);
            color: var(--text-muted);
            padding: 8px 14px;
            border-radius: 12px;
            cursor: pointer;
            font-size: .85rem;
            width: 100%;
            justify-content: center;
            margin-top: 4px;
            transition: all .2s;
        }
        .add-img-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }
        .img-label-primary {
            font-size: .7rem;
            color: var(--accent);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .color-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }
        .color-row input[type="color"] {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: none;
            cursor: pointer;
            padding: 2px;
        }
        .color-row .remove-color-btn {
            background: none;
            border: 1px solid var(--danger);
            color: var(--danger);
            border-radius: 8px;
            width: 34px;
            height: 34px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .add-color-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: 1px dashed var(--border-color);
            color: var(--text-muted);
            padding: 8px 14px;
            border-radius: 12px;
            cursor: pointer;
            font-size: .85rem;
            width: 100%;
            justify-content: center;
            margin-top: 4px;
        }
        .add-color-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
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
        <a href="index.php">Dashboard Admin</a>
        <a href="products.php">Produits</a>
        <a href="orders.php">Commandes</a>
        <a href="../logout.php">Déconnexion</a>
    </div>
</nav>

<main class="main-content">

<div class="admin-layout">

    <nav class="admin-sidebar">
        <a href="index.php"><i class="bx bx-grid-alt"></i> Dashboard</a>
        <a href="products.php" class="active"><i class="bx bx-package"></i> Produits</a>
        <a href="categories.php"><i class="bx bx-category"></i> Catégories</a>
        <a href="orders.php"><i class="bx bx-receipt"></i> Commandes</a>
        <a href="users.php"><i class="bx bx-group"></i> Utilisateurs</a>
        <a href="../index.php"><i class="bx bx-store"></i> Voir la boutique</a>
        <a href="../logout.php"><i class="bx bx-log-out"></i> Déconnexion</a>
    </nav>

    <div class="admin-main">

        <?php if ($msg != '') { ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
        <?php } ?>

        <div style="display:grid;grid-template-columns:1fr 420px;gap:32px;align-items:start">

            <div>
                <h2 class="section-title">Produits (<?php echo count($products); ?>)</h2>
                <table class="table">
                    <thead>
                        <tr><th>ID</th><th>Photo</th><th>Nom</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p) { ?>
                            <tr>
                                <td><?php echo $p['product_id']; ?></td>
                                <td>
                                    <?php if ($p['image']) { ?>
                                        <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:8px;">
                                    <?php } ?>
                                </td>
                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                <td><?php echo htmlspecialchars($p['cat_name']); ?></td>
                                <td><?php echo number_format($p['price'], 2, ',', ' '); ?> DA</td>
                                <td><?php echo $p['stock']; ?></td>
                                <td style="display:flex;gap:8px">
                                    <a href="?edit=<?php echo $p['product_id']; ?>" class="btn btn-ghost" style="padding:6px 12px;font-size:.8rem">Éditer</a>
                                    <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer ?')">
                                        <button type="submit" name="delete" value="<?php echo $p['product_id']; ?>" class="btn btn-danger" style="padding:6px 12px;font-size:.8rem">Suppr.</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h3 style="margin-bottom:20px;font-weight:900">
                    <?php if ($edit) echo 'Modifier'; else echo 'Nouveau'; ?> produit
                </h3>
                <form method="POST" id="product-form">
                    <?php if ($edit) { ?>
                        <input type="hidden" name="product_id" value="<?php echo $edit['product_id']; ?>">
                    <?php } ?>

                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="category_id" class="large-input">
                            <?php foreach ($categories as $c) { ?>
                                <option value="<?php echo $c['category_id']; ?>" <?php if ($edit && $edit['category_id'] == $c['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nom *</label>
                        <input type="text" name="name" class="large-input" value="<?php if ($edit) echo htmlspecialchars($edit['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3" class="large-input"><?php if ($edit) echo htmlspecialchars($edit['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Prix (DA) *</label>
                        <input type="number" step="0.01" name="price" class="large-input" value="<?php if ($edit) echo $edit['price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stock *</label>
                        <input type="number" name="stock" class="large-input" value="<?php if ($edit) echo $edit['stock']; else echo '0'; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Marque</label>
                        <input type="text" name="brand" class="large-input" value="<?php if ($edit) echo htmlspecialchars($edit['brand']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Couleurs <span style="color:var(--text-muted);font-weight:400">— optionnel, laisser vide si pas de couleur</span></label>
                        <div id="colors-container">
                            <?php if ($edit && !empty($edit_colors)) {
                                foreach ($edit_colors as $ci => $col) { ?>
                                    <div class="color-row" id="color-row-<?php echo $ci; ?>">
                                        <input type="color" name="color_hexes[]" value="<?php echo htmlspecialchars($col['color_hex']); ?>">
                                        <input type="text" name="color_names[]" class="large-input" value="<?php echo htmlspecialchars($col['color_name']); ?>" placeholder="Nom de la couleur (ex: Noir)">
                                        <button type="button" class="remove-color-btn" onclick="removeColorRow(this)"><i class="bx bx-x"></i></button>
                                    </div>
                                <?php }
                            } ?>
                        </div>
                        <button type="button" class="add-color-btn" onclick="addColorRow()"><i class="bx bx-palette"></i> Ajouter une couleur</button>
                    </div>

                    <div class="form-group">
                        <label>Taille</label>
                        <input type="text" name="size" class="large-input" value="<?php if ($edit) echo htmlspecialchars($edit['size']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Images (URLs) <span style="color:var(--text-muted);font-weight:400">— la 1ère est l'image principale</span></label>
                        <div id="images-container">
                            <?php if ($edit && !empty($edit_images)) {
                                foreach ($edit_images as $i => $url) { ?>
                                    <div class="img-row" id="img-row-<?php echo $i; ?>">
                                        <?php if ($i === 0) echo '<span class="img-label-primary">★</span>'; else echo '<span style="width:16px"></span>'; ?>
                                        <img src="<?php echo htmlspecialchars($url); ?>" class="preview-thumb" onerror="this.style.display='none'">
                                        <input type="text" name="images[]" class="large-input img-url-input" value="<?php echo htmlspecialchars($url); ?>" placeholder="https://..." oninput="updateThumb(this)">
                                        <button type="button" class="remove-img-btn" onclick="removeImgRow(this)"><i class="bx bx-x"></i></button>
                                    </div>
                                <?php }
                            } else { ?>
                                <div class="img-row" id="img-row-0">
                                    <span class="img-label-primary">★</span>
                                    <img src="" class="preview-thumb" style="display:none">
                                    <input type="text" name="images[]" class="large-input img-url-input" placeholder="https://..." oninput="updateThumb(this)">
                                    <button type="button" class="remove-img-btn" onclick="removeImgRow(this)"><i class="bx bx-x"></i></button>
                                </div>
                            <?php } ?>
                        </div>
                        <button type="button" class="add-img-btn" onclick="addImageRow()"><i class="bx bx-plus"></i> Ajouter une image</button>
                    </div>

                    <button type="submit" name="save" class="btn btn-primary btn-full">
                        <?php if ($edit) echo 'Mettre à jour'; else echo 'Créer'; ?>
                    </button>
                    <?php if ($edit) { ?>
                        <a href="products.php" class="btn btn-ghost btn-full" style="margin-top:8px">Annuler</a>
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
<script>
function addImageRow() {
    var container = document.getElementById('images-container');
    var idx = container.querySelectorAll('.img-row').length;
    var div = document.createElement('div');
    div.className = 'img-row';
    div.id = 'img-row-' + idx;
    div.innerHTML = '<span style="width:16px"></span>' +
        '<img src="" class="preview-thumb" style="display:none">' +
        '<input type="text" name="images[]" class="large-input img-url-input" placeholder="https://..." oninput="updateThumb(this)">' +
        '<button type="button" class="remove-img-btn" onclick="removeImgRow(this)"><i class="bx bx-x"></i></button>';
    container.appendChild(div);
    div.querySelector('input').focus();
    updateStarLabels();
}

function removeImgRow(btn) {
    var row = btn.closest('.img-row');
    var container = document.getElementById('images-container');
    if (container.querySelectorAll('.img-row').length > 1) {
        row.remove();
        updateStarLabels();
    } else {
        row.querySelector('input').value = '';
        row.querySelector('.preview-thumb').style.display = 'none';
    }
}

function updateStarLabels() {
    var rows = document.querySelectorAll('#images-container .img-row');
    rows.forEach(function(row, i) {
        var span = row.querySelector('span');
        if (i === 0) {
            span.className = 'img-label-primary';
            span.textContent = '★';
        } else {
            span.className = '';
            span.style.width = '16px';
            span.textContent = '';
        }
    });
}

function updateThumb(input) {
    var row = input.closest('.img-row');
    var thumb = row.querySelector('.preview-thumb');
    var url = input.value.trim();
    if (url) {
        thumb.src = url;
        thumb.style.display = 'block';
        thumb.onerror = function() { this.style.display = 'none'; };
    } else {
        thumb.style.display = 'none';
    }
}

function addColorRow() {
    var container = document.getElementById('colors-container');
    var idx = container.querySelectorAll('.color-row').length;
    var div = document.createElement('div');
    div.className = 'color-row';
    div.id = 'color-row-' + idx;
    div.innerHTML =
        '<input type="color" name="color_hexes[]" value="#3b82f6">' +
        '<input type="text" name="color_names[]" class="large-input" placeholder="Nom de la couleur (ex: Bleu)">' +
        '<button type="button" class="remove-color-btn" onclick="removeColorRow(this)"><i class="bx bx-x"></i></button>';
    container.appendChild(div);
    div.querySelector('input[type="text"]').focus();
}

function removeColorRow(btn) {
    var row = btn.closest('.color-row');
    row.remove();
}
</script>
</body>
</html>
