<?php
?>
<section class="hero">
    <div class="hero-content">
        <div class="hero-badge">La garde d'animaux par des professionnels</div>
        <h1 class="hero-title">Trouvez un gardien<br>de confiance pour<br><em>votre compagnon</em></h1>
        <p class="hero-sub">Des professionnels certifiés près de chez vous, prêts à accueillir votre animal avec tout l'amour et l'expertise qu'il mérite.</p>
        <div class="hero-actions">
            <a href="index.php?action=annonces" class="btn btn-primary btn-lg">Voir les pet sitter</a>
            <?php if (!isLoggedOn()): ?>
                <a href="index.php?action=inscription" class="btn btn-outline btn-lg">Devenir gardien pro</a>
            <?php elseif (isPro()): ?>
                <a href="index.php?action=creerAnnonce" class="btn btn-outline btn-lg">Mon profil pro</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-visual">
        <div class="hero-carousel">
            <div class="carousel-track">
                <div class="carousel-slide active">
                    <img src="<?= BASE_URL ?>/images/carrousel/chien.jpg" alt="chien">
                </div>
                <div class="carousel-slide">
                    <img src="<?= BASE_URL ?>/images/carrousel/chat.jpg" alt="chat">
                </div>
                <div class="carousel-slide">
                    <img src="<?= BASE_URL ?>/images/carrousel/hamster.jpg" alt="hamster">
                </div>
                <div class="carousel-slide">
                    <img src="<?= BASE_URL ?>/images/carrousel/oiseau.jpg" alt="oiseau">
                </div>
                <div class="carousel-slide">
                    <img src="<?= BASE_URL ?>/images/carrousel/chien2.jpg" alt="chien">
                </div>
            </div>
            <div class="carousel-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($pros)): ?>
<section class="latest-annonces">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Nos pet sitter</span>
            <h2>Des professionnels qui accueillent vos animaux</h2>
            <a href="index.php?action=annonces" class="see-all">Voir tous →</a>
        </div>
        <div class="annonces-grid">
            <?php foreach ($pros as $pro): ?>
            <?php include __DIR__ . '/../annonces/_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <h2>Vous êtes un professionnel de l'animal ?</h2>
            <p>Rejoignez Paw Paw et proposez vos services à des propriétaires qui vous font confiance.</p>
            <?php if (!isLoggedOn()): ?>
                <a href="index.php?action=inscription" class="btn btn-primary btn-lg">Créer mon profil pro gratuitement</a>
            <?php elseif (isPro()): ?>
                <a href="index.php?action=creerAnnonce" class="btn btn-primary btn-lg">Compléter mon profil</a>
            <?php endif; ?>
        </div>
    </div>
</section>
