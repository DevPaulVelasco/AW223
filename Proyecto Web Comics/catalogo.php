<?php
/**
 * KomiX - Catálogo de Cómics
 * Archivo: catalogo.php (Página 12 del diseño)
 */
require_once 'includes/auth.php';
$pagina_actual = 'comics';
$titulo_pagina = 'KomiX - Catálogo';
include 'includes/header.php';
include 'includes/sidebar.php';

$comics_catalogo = [
    [
        'id' => 1,
        'titulo' => 'Classic Comic Book Action',
        'precio' => '$150.00',
        'imagen' => './images/comic_action.jpg'
    ],
    [
        'id' => 2,
        'titulo' => 'The Incredible Hulk vs Wolverine',
        'precio' => '$280.00',
        'imagen' => './images/comic_hulk.jpg'
    ],
    [
        'id' => 3,
        'titulo' => 'X-Men: Mutant Genesis #1',
        'precio' => '$220.00',
        'imagen' => './images/comic_xmen.jpg'
    ],
    [
        'id' => 4,
        'titulo' => 'Justice League: New 52 #1',
        'precio' => '$195.00',
        'imagen' => './images/comic_justice.jpg'
    ]
];
?>

<main class="main-content">
    <div class="content-header-row" style="margin-bottom: 24px;">
        <h1 class="page-title">Catalogo</h1>
        <div class="busqueda-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" class="input-busqueda-pill" placeholder="Buscador">
        </div>
    </div>

    <!-- Cuadrícula de Portadas de Cómics -->
    <div class="catalogo-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 300px)); justify-content: center; gap: 32px;">
        <?php foreach ($comics_catalogo as $comic): ?>
            <div class="comic-card catalogo-card">
                <img 
                    src="<?php echo htmlspecialchars($comic['imagen']); ?>" 
                    alt="<?php echo htmlspecialchars($comic['titulo']); ?>" 
                    class="comic-cover-img"
                    onerror="this.onerror=null; this.src='assets/images/comic_action.png';"
                >
                <div class="comic-card-info">
                    <span class="comic-card-title"><?php echo htmlspecialchars($comic['titulo']); ?></span>
                    <span class="comic-card-price"><?php echo htmlspecialchars($comic['precio']); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
