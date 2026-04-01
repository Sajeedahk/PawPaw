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
$typeEmojis = [
    'chien'   => '',
    'chat'    => '',
    'lapin'   => '',
    'oiseau'  => '',
    'rongeur' => '',
    'reptile' => '',
    'poisson' => '',
    'autre'   => '',
];
?>
<div class="page-header">
    <div class="container">
        <h1>Nos gardiens professionnels</h1>
        <p><?= count($pros) ?> gardien<?= count($pros) > 1 ? 's' : '' ?> disponible<?= count($pros) > 1 ? 's' : '' ?></p>
    </div>
</div>

<div class="container">
    <form method="GET" action="index.php" class="search-bar">
        <input type="hidden" name="action" value="annonces">
        <div class="search-inputs">
            <div class="search-field">
                <input type="text" name="ville" placeholder="Ville, quartier..."
                       value="<?= htmlspecialchars($ville) ?>">
            </div>
            <select name="type" class="search-select">
                <option value="">Tous les animaux</option>
                <?php foreach ($typeEmojis as $val => $emoji): ?>
                    <option value="<?= $val ?>" <?= $animal_type === $val ? 'selected' : '' ?>>
                        <?= $emoji . ' ' . ucfirst($val) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Filtrer</button>
            <?php if ($ville || $animal_type): ?>
                <a href="index.php?action=annonces" class="btn btn-ghost">Réinitialiser</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (empty($pros)): ?>
        <div class="empty-state">
            <div class="empty-icon"><img src="<?= BASE_URL ?>/images/icons/autre.png" alt="autre"></div>
            <h3>Aucun gardien trouvé</h3>
            <p>Essayez d'autres critères de recherche.</p>
        </div>
    <?php else: ?>
        <div class="annonces-grid">
            <?php foreach ($pros as $pro): ?>
                <?php include __DIR__ . '/_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
