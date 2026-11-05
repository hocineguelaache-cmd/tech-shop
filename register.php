<?php
require_once __DIR__ . '/config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $addr  = trim($_POST['address'] ?? '');

    if (empty($name) || empty($email) || empty($pass)) {
        $err = 'Tous les champs obligatoires (*) sont requis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Email invalide.';
    } elseif (strlen($pass) < 6) {
        $err = 'Mot de passe trop court (6 caractères minimum).';
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $err = 'Cet email est déjà utilisé.';
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password, phone, address, role)
                    VALUES (?, ?, ?, ?, ?, 'client')";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$name, $email, $hashed, $phone, $addr])) {
                $userId = $pdo->lastInsertId();
                session_regenerate_id(true);
                $_SESSION['user_id'] = $userId;
                $_SESSION['name']    = $name;
                $_SESSION['role']    = 'client';
                header('Location: index.php');
                exit;
            } else {
                $err = "Erreur lors de la création du compte.";
            }
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
    <title>Inscription - TECH-SHOP</title>
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
                <?php if ($cart_count > 0) echo '<span class="badge">'.$cart_count.'</span>'; ?>
            </a>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <a href="profile.php"><i class="bx bx-user"></i></a>
                <?php if ($_SESSION['role'] === 'admin') echo '<a href="admin/index.php"><i class="bx bx-shield"></i></a>'; ?>
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
        <a href="login.php">Se connecter</a>
    </div>
</nav>

<main class="main-content">
    <div class="auth-wrap">
        <div class="auth-box">
            <h1 class="auth-title">Créer un compte</h1>
            <p class="auth-sub">Rejoignez TECH-SHOP dès aujourd'hui</p>
            <?php if ($err) echo '<div class="alert alert-error">'.htmlspecialchars($err).'</div>'; ?>
            <form method="POST" id="registerForm">
                <div class="form-group"><label>Nom *</label><input type="text" name="name" class="large-input" required></div>
                <div class="form-group"><label>Email *</label><input type="email" name="email" class="large-input" required></div>
                <div class="form-group"><label>Mot de passe *</label><input type="password" name="password" class="large-input" required></div>
                <div class="form-group"><label>Téléphone</label><input type="text" name="phone" class="large-input"></div>
                <div class="form-group"><label>Adresse</label><textarea name="address" rows="2" class="large-input"></textarea></div>
                <button type="submit" class="btn btn-primary btn-full" id="registerBtn">S'inscrire</button>
            </form>
            <p style="margin-top:20px; text-align:center">Déjà un compte ? <a href="login.php">Se connecter</a></p>
            <script>
                document.getElementById('registerForm').addEventListener('submit', function() {
                    var btn = document.getElementById('registerBtn');
                    btn.disabled = true;
                    btn.innerText = 'Inscription en cours...';
                });
            </script>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="footer-inner">
        <img class="nav-logo-img" src="logo.png" style="height:32px;">
        <p>&copy; <?php echo date('Y'); ?> TECH-SHOP</p>
        <a href="login.php" style="color:var(--text-muted);">Espace Admin</a>
    </div>
</footer>
<script src="assets/js/main.js"></script>
</body>
</html>
