<?php
$typeImages = [
    'chien'   => BASE_URL . '/images/icons/dog.png',
    'chat'    => BASE_URL . '/images/icons/animal.png',
    'lapin'   => BASE_URL . '/images/icons/rabbit.png',
    'oiseau'  => BASE_URL . '/images/icons/pigeon.png',
    'rongeur' => BASE_URL . '/images/icons/rodent.png',
    'reptile' => BASE_URL . '/images/icons/reptile.png',
    'poisson' => BASE_URL . '/images/icons/poisson.png',
    'autre'   => BASE_URL . '/images/icons/autre.png',
];

?>
<article class="annonce-card">
    <a href="index.php?action=annonce&id=<?= $pro['id'] ?>" class="card-link">
        <div class="card-photo">
            <?php if (!empty($pro['photo'])): ?>
                <img src="<?= BASE_URL ?>/images/uploads/<?= htmlspecialchars($pro['photo']) ?>"
                     alt="<?= htmlspecialchars($pro['prenom']) ?>">
            <?php else: ?>
                <div class="card-photo-placeholder">
                    <span class="pro-initiale"><?= mb_strtoupper(mb_substr($pro['prenom'], 0, 1)) ?></span>
                </div>
            <?php endif; ?>
            <span class="card-status status-available">Disponible</span>
        </div>
        <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($pro['nom_structure'] ?? ($pro['prenom'] . ' ' . $pro['nom'])) ?></h3>
            <p class="card-animal"><?= htmlspecialchars($pro['prenom'] . ' ' . $pro['nom']) ?></p>
            <div class="card-animaux-tags">
                <?php foreach (explode(',', $pro['animaux_acceptes']) as $a): ?>
                    <span class="animal-badge"><img src="<?= $typeImages[trim($a)] ?? '' ?>" class="animal-img-icon" alt=""> <?= ucfirst(trim($a)) ?></span>
                <?php endforeach; ?>
            </div>
            <div class="card-info">
                <span class="info-item"><img src="<?= BASE_URL ?>/images/icons/pin.png" alt="pin"><?= htmlspecialchars($pro['ville']) ?></span>
            </div>
        </div>
    </a>
</article>
