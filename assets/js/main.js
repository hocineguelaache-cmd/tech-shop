// ===== BOUTONS + ET - POUR LA QUANTITE =====
document.querySelectorAll('.qty-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var input = btn.parentElement.querySelector('.qty-input');
        var val = parseInt(input.value) || 1;
        if (btn.dataset.action === 'inc') {
            val = val + 1;
        } else if (btn.dataset.action === 'dec' && val > 1) {
            val = val - 1;
        }
        input.value = val;
    });
});

// ===== BOUTONS DE FILTRE =====
document.querySelectorAll('.filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        btn.classList.toggle('active');
    });
});

// Le thème light/dark est géré côté serveur via toggle_theme.php (cookie PHP).
// Aucun JavaScript nécessaire pour le changement de thème.

// ===== MENU HAMBURGER MOBILE =====
function toggleMenu() {
    var menu = document.getElementById('mobile-menu');
    var icon = document.getElementById('hamburger-icon');
    if (!menu) { return; }

    var isOpen = menu.classList.contains('open');
    menu.classList.toggle('open');

    // Changer l'icône entre menu (☰) et fermer (✕)
    if (icon) {
        if (isOpen) {
            icon.className = 'bx bx-menu';
        } else {
            icon.className = 'bx bx-x';
        }
    }
}

// Fermer le menu mobile en cliquant en dehors
document.addEventListener('click', function(e) {
    var menu = document.getElementById('mobile-menu');
    var hamburger = document.getElementById('hamburger');
    if (!menu || !hamburger) { return; }

    // Si le clic n'est pas dans le hamburger ni dans le menu → fermer
    if (!hamburger.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.remove('open');
        var icon = document.getElementById('hamburger-icon');
        if (icon) { icon.className = 'bx bx-menu'; }
    }
});

// Fermer le menu si on clique sur un lien à l'intérieur
document.addEventListener('click', function(e) {
    if (e.target && e.target.closest && e.target.closest('.mobile-menu a')) {
        var menu = document.getElementById('mobile-menu');
        var icon = document.getElementById('hamburger-icon');
        if (menu) { menu.classList.remove('open'); }
        if (icon) { icon.className = 'bx bx-menu'; }
    }
});
