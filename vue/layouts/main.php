<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titre ?? 'Paw Paw') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="index.php?action=defaut" class="brand">
            <span class="hero-emoji e1">
                <img src="<?= BASE_URL ?>/images/icons/logo.png" alt="logo">
            </span>
            <span class="brand-name">Paw <em>Paw</em></span>
        </a>
        <div class="nav-links">
            <a href="index.php?action=annonces" class="nav-link">Pet sitter</a>
            <?php if (isLoggedOn()): ?>
                <a href="index.php?action=profil" class="nav-link nav-profile">
                    <?php if (!empty($_SESSION['user_photo'])): ?>
                        <span class="avatar-pill avatar-pill-img">
                            <img src="<?= BASE_URL ?>/images/uploads/<?= htmlspecialchars($_SESSION['user_photo']) ?>" alt="PP">
                        </span>
                    <?php else: ?>
                        <span class="avatar-pill"><?= mb_strtoupper(mb_substr($_SESSION['user_nom'], 0, 1)) ?></span>
                    <?php endif; ?>
                    <?= htmlspecialchars($_SESSION['user_nom']) ?>
                </a>
                <a href="index.php?action=deconnexion" class="nav-link nav-logout">Déconnexion</a>
            <?php else: ?>
                <a href="index.php?action=login" class="nav-link">Connexion</a>
                <a href="index.php?action=inscription" class="nav-link nav-cta">S'inscrire</a>
            <?php endif; ?>
        </div>
        <button class="burger" id="burger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
    <div class="nav-mobile" id="navMobile">
        <a href="index.php?action=annonces">Pet sitter</a>
        <?php if (isLoggedOn()): ?>
            <a href="index.php?action=profil">Mon espace</a>
            <a href="index.php?action=deconnexion">Déconnexion</a>
        <?php else: ?>
            <a href="index.php?action=login">Connexion</a>
            <a href="index.php?action=inscription">S'inscrire</a>
        <?php endif; ?>
    </div>
</nav>

<main class="main-content">
    <?= $content ?>
</main>

<footer class="footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <img src="<?= BASE_URL ?>/images/icons/logo.png" alt="logo">
            <strong>Paw Paw</strong>
            <p>La garde d'animaux par des professionnels passionnés</p>
        </div>
        <div class="footer-links">
            <a href="index.php?action=annonces">Nos pet sitter</a>
            <?php if (isLoggedOn()): ?>
                <a href="index.php?action=profil">Mon espace</a>
            <?php else: ?>
                <a href="index.php?action=inscription">Rejoindre Paw Paw</a>
            <?php endif; ?>
        </div>
        <p class="footer-copy">© <?= date('Y') ?> Paw Paw</p>
    </div>
</footer>

<script src="<?= BASE_URL ?>/JS/app.js"></script>
<script>
    // Carousel
    const slides = document.querySelectorAll('.carousel-slide');
    const dots   = document.querySelectorAll('.dot');
    if (slides.length > 0) {
        let current = 0, timer;
        function goTo(i) {
            slides[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = (i + slides.length) % slides.length;
            slides[current].classList.add('active');
            dots[current].classList.add('active');
        }
        function startAuto() { timer = setInterval(() => goTo(current + 1), 4000); }
        function stopAuto()  { clearInterval(timer); }
        dots.forEach((dot, i) => dot.addEventListener('click', () => { stopAuto(); goTo(i); startAuto(); }));
        startAuto();
    }

    // Burger menu
    const burger    = document.getElementById('burger');
    const navMobile = document.getElementById('navMobile');
    if (burger) burger.addEventListener('click', () => navMobile.classList.toggle('open'));

    // Toggle mot de passe
    function togglePwd(id) {
        const f = document.getElementById(id);
        f.type = f.type === 'password' ? 'text' : 'password';
    }
</script>
</body>
</html>
