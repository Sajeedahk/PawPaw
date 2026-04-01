<div class="page-header">
    <div class="container">
        <h1>Modifier l'annonce</h1>
    </div>
</div>

<div class="container">
    <div class="form-layout">
        <form method="POST" action="index.php?action=modifierAnnonce&id=<?= $annonce['id'] ?>" class="main-form">

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="form-section">
                <h2 class="form-section-title">Mon animal</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Prénom de l'animal <span class="req">*</span></label>
                        <input type="text" name="animal_nom" required
                               value="<?= htmlspecialchars($annonce['animal_nom']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Type d'animal <span class="req">*</span></label>
                        <select name="animal_type" required>
                            <?php foreach (['chien'=>'Chien','chat'=>'Chat','lapin'=>'Lapin','oiseau'=>'Oiseau','rongeur'=>'Rongeur','reptile'=>'Reptile','poisson'=>'Poisson','autre'=>'Autre'] as $val => $label): ?>
                                <option value="<?= $val ?>" <?= $annonce['animal_type'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php if (!empty($annonce['animal_photo'])): ?>
                <div class="form-group">
                    <label>Photo actuelle</label>
                    <img src="<?= BASE_URL ?>/images/uploads/<?= htmlspecialchars($annonce['animal_photo']) ?>"
                         alt="Photo actuelle" style="max-width:200px; border-radius:12px; display:block; margin-top:8px;">
                </div>
                <?php endif; ?>
            </div>

            <div class="form-section">
                <h2 class="form-section-title">L'annonce</h2>
                <div class="form-group">
                    <label>Titre <span class="req">*</span></label>
                    <input type="text" name="titre" required maxlength="255"
                           value="<?= htmlspecialchars($annonce['titre']) ?>">
                </div>
                <div class="form-group">
                    <label>Description <span class="req">*</span></label>
                    <textarea name="description" required rows="5"><?= htmlspecialchars($annonce['description']) ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h2 class="form-section-title">Lieu & Horaires</h2>
                <div class="form-group">
                    <label>Localisation <span class="req">*</span></label>
                    <input type="text" name="localisation" required
                           value="<?= htmlspecialchars($annonce['localisation']) ?>">
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Début <span class="req">*</span></label>
                        <input type="datetime-local" name="date_debut" required
                               value="<?= date('Y-m-d\TH:i', strtotime($annonce['date_debut'])) ?>">
                    </div>
                    <div class="form-group">
                        <label>Fin <span class="req">*</span></label>
                        <input type="datetime-local" name="date_fin" required
                               value="<?= date('Y-m-d\TH:i', strtotime($annonce['date_fin'])) ?>">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <label>Prix par heure (€) <span class="req">*</span></label>
                    <div class="input-prefix">
                        <span>€</span>
                        <input type="number" name="prix_heure" required min="0.5" step="0.5"
                               value="<?= htmlspecialchars($annonce['prix_heure']) ?>">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php?action=annonce&id=<?= $annonce['id'] ?>" class="btn btn-ghost">Annuler</a>
                <button type="submit" class="btn btn-primary btn-lg">Sauvegarder</button>
            </div>
        </form>
    </div>
</div>
