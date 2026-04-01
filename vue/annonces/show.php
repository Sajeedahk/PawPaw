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
$estLePro   = isLoggedOn() && $_SESSION['user_id'] == $pro['id'];
// Animaux acceptés par ce pro (uniquement ceux-là seront proposés à la réservation)
$animauxAcceptes = array_map('trim', explode(',', $pro['animaux_acceptes']));
?>
<div class="container">
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="annonce-detail">
        <div class="annonce-main">

            <!-- Photo / Avatar -->
            <div class="annonce-photo-wrap">
                <?php if (!empty($pro['profil_photo'])): ?>
                    <img src="<?= BASE_URL ?>/images/uploads/<?= htmlspecialchars($pro['profil_photo']) ?>"
                         alt="<?= htmlspecialchars($pro['prenom']) ?>" class="annonce-photo">
                <?php else: ?>
                    <div class="annonce-photo-placeholder pro-initiale-xl">
                        <?= mb_strtoupper(mb_substr($pro['prenom'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="annonce-statut-badge statut-disponible">
                    <span class="statut-dot"></span>
                    <span class="statut-text">Professionnel actif</span>
                </div>
            </div>

            <!-- Infos -->
            <div class="annonce-info-block">
                <div class="annonce-tags">
                    <?php foreach (explode(',', $pro['animaux_acceptes']) as $a): ?>
                        <span class="animal-badge"><img src="<?= $typeImages[trim($a)] ?? '' ?>" class="animal-img-icon" alt=""> <?= ucfirst(trim($a)) ?></span>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($pro['nom_structure'])): ?>
                    <h1 class="annonce-title"><?= htmlspecialchars($pro['nom_structure']) ?></h1>
                    <p class="annonce-animal-name">Géré par <strong><?= htmlspecialchars($pro['prenom'] . ' ' . $pro['nom']) ?></strong></p>
                <?php else: ?>
                    <h1 class="annonce-title"><?= htmlspecialchars($pro['prenom'] . ' ' . $pro['nom']) ?></h1>
                <?php endif; ?>

                <div class="annonce-meta-grid">
                    <div class="meta-item">
                        <span class="meta-icon"><img src="<?= BASE_URL ?>/images/icons/pin.png" alt="maison"></span>
                        <div>
                            <small>Adresse</small>
                            <strong><?= htmlspecialchars($pro['adresse'] . ', ' . $pro['ville']) ?></strong>
                        </div>
                    </div>
                    <div class="meta-item">
                        <span class="meta-icon"><img src="<?= BASE_URL ?>/images/icons/home.png" alt="maison"></span>
                        <div>
                            <small>Capacité</small>
                            <strong><?= $pro['capacite_max'] ?> anim<?= $pro['capacite_max'] > 1 ? 'aux' : 'al' ?> max</strong>
                        </div>
                    </div>
                    <?php if ($pro['telephone']): ?>
                    <div class="meta-item">
                        <span class="meta-icon"><img src="<?= BASE_URL ?>/images/icons/phone.png" alt="téléphone"></span>
                        <div>
                            <small>Téléphone</small>
                            <strong><?= htmlspecialchars($pro['telephone']) ?></strong>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($pro['bio']): ?>
                <div class="annonce-description">
                    <h3>À propos</h3>
                    <p><?= nl2br(htmlspecialchars($pro['bio'])) ?></p>
                </div>
                <?php endif; ?>

                <!-- Tarifs -->
                <?php if (!empty($tarifs)): ?>
                <div class="annonce-description">
                    <h3>Tarifs</h3>
                    <div class="tarifs-grid">
                        <?php foreach ($tarifs as $type => $prix): ?>
                        <div class="tarif-item">
                            <span class="animal-badge"><img src="<?= $typeImages[trim($a)] ?? '' ?>" class="animal-img-icon" alt=""> <?= ucfirst(trim($a)) ?></span>
                            <strong><?= number_format($prix, 2) ?> €/h</strong>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Créneaux disponibles — Calendrier -->
                <div class="annonce-description">
                    <h3>Choisir une date</h3>
                    <?php if (empty($creneaux)): ?>
                        <p class="text-muted">Aucun créneau disponible pour le moment.</p>
                    <?php else: ?>
                        <?php
                        $creneauxJS = [];
                        foreach ($creneaux as $c) {
                            $capaciteMax  = (int)($c['capacite_max'] ?? 1);
                            $placesLibres = (int)($c['places_libres'] ?? $capaciteMax);
                            $creneauxJS[] = [
                                'id'           => $c['id'],
                                'debut'        => $c['date_debut'],
                                'fin'          => $c['date_fin'],
                                'statut'       => $c['statut'],
                                'placesLibres' => max(0, $placesLibres),
                                'capaciteMax'  => $capaciteMax,
                                'heureDebut'   => date('H:i', strtotime($c['date_debut'])),
                                'heureFin'     => date('H:i', strtotime($c['date_fin'])),
                                'labelDebut'   => date('d/m/Y', strtotime($c['date_debut'])),
                                'labelFin'     => date('d/m/Y', strtotime($c['date_fin'])),
                                'dateDebut'    => date('Y-m-d', strtotime($c['date_debut'])),
                                'dateFin'      => date('Y-m-d', strtotime($c['date_fin'])),
                            ];
                        }
                        ?>
                        <div class="cal-wrap">
                            <div class="cal-header">
                                <button type="button" class="cal-nav" id="calPrev">&#8249;</button>
                                <span class="cal-month-label" id="calMonthLabel"></span>
                                <button type="button" class="cal-nav" id="calNext">&#8250;</button>
                            </div>
                            <div class="cal-grid cal-weekdays">
                                <span>Lun</span><span>Mar</span><span>Mer</span>
                                <span>Jeu</span><span>Ven</span><span>Sam</span><span>Dim</span>
                            </div>
                            <div class="cal-grid cal-days" id="calDays"></div>

                            <!-- Légende -->
                            <div class="cal-legende">
                                <span class="leg-item"><span class="leg-dot leg-dispo"></span> Disponible</span>
                                <span class="leg-item"><span class="leg-dot leg-reserve"></span> Complet</span>
                                <?php if (!$estLePro && isLoggedOn() && isParticulier()): ?>
                                <button type="button" id="calResetBtn" class="btn btn-ghost btn-sm" style="margin-left:auto;font-size:.75rem;padding:.2rem .7rem">↺ Réinitialiser</button>
                                <?php endif; ?>
                            </div>

                            <!-- Instructions / état sélection -->
                            <div class="cal-creneaux" id="calCreneaux" style="display:none">
                                <h4 id="calCreneauxTitle"></h4>
                                <div class="cal-slots" id="calSlotsList"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="annonce-sidebar">
            <?php if ($estLePro): ?>
            <div class="sidebar-card sidebar-owner-actions">
                <h3>Mon profil pro</h3>
                <a href="index.php?action=creerAnnonce" class="btn btn-outline btn-block">Modifier mon profil</a>
                <a href="index.php?action=profil" class="btn btn-outline btn-block">Mon tableau de bord</a>
            </div>

            <?php elseif (!isLoggedOn()): ?>
            <div class="sidebar-card sidebar-auth">
                <h3>Envie de confier votre animal ?</h3>
                <p>Connectez-vous pour réserver un créneau.</p>
                <a href="index.php?action=login" class="btn btn-primary btn-block">Se connecter</a>
                <a href="index.php?action=inscription" class="btn btn-outline btn-block">S'inscrire gratuitement</a>
            </div>

            <?php elseif (isPro()): ?>
            <div class="sidebar-card sidebar-info">
                <span class="info-icon"><img src="<?= BASE_URL ?>/images/icons/info.png" alt="téléphone"></span>
                <h3>Compte professionnel</h3>
                <p>Seuls les propriétaires peuvent effectuer des réservations.</p>
            </div>

            <?php else: ?>
            <div class="sidebar-card sidebar-info">
                <span class="info-icon"><img src="<?= BASE_URL ?>/images/icons/info.png" alt="téléphone"></span>
                <h3>Comment réserver ?</h3>
                <p>Choisissez un créneau disponible et cliquez sur <strong>Réserver</strong>.</p>
            </div>
            <?php endif; ?>
        </aside>
    </div>
</div>

<!-- Modal réservation -->
<div class="modal-overlay" id="modalReservation" style="display:none">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Réserver une période</h3>
            <button class="modal-close" onclick="closeReservation()">✕</button>
        </div>
        <p id="modalDates" class="modal-dates"></p>
        <form method="POST" id="formReservation" action="index.php?action=reserver" enctype="multipart/form-data">
            <input type="hidden" name="mode" value="range">
            <input type="hidden" name="pro_id" value="<?= $pro['id'] ?>">
            <input type="hidden" name="date_debut" id="hiddenDebut">
            <input type="hidden" name="date_fin"   id="hiddenFin">
            <div class="form-group">
                <label>Prénom de votre animal <span class="req">*</span></label>
                <input type="text" name="animal_nom" required placeholder="Ex: Rex, Mimi...">
            </div>
            <div class="form-group">
                <label>Type d'animal <span class="req">*</span></label>
                <select name="animal_type" required>
                    <option value="">-- Choisir --</option>
                    <?php foreach ($typeEmojis as $val => $emoji): ?>
                        <?php if (in_array($val, $animauxAcceptes)): ?>
                            <option value="<?= $val ?>"><?= $emoji . ' ' . ucfirst($val) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <small class="form-hint">Ce gardien accepte uniquement :
                    <?= implode(', ', array_map(fn($a) => ($typeEmojis[$a] ?? '') . ' ' . ucfirst($a), $animauxAcceptes)) ?>
                </small>
            </div>
            <div class="form-group">
                <label>📷 Photo de votre animal <small class="form-hint">(optionnel · JPG/PNG · max 5 Mo)</small></label>
                <div class="photo-upload-zone" id="photoUploadZone" onclick="document.getElementById('animalPhotoInput').click()">
                    <img id="photoPreview" src="" alt="" style="display:none; max-height:140px; border-radius:8px; object-fit:cover;">
                    <div id="photoPlaceholder">
                        <span style="font-size:2rem">🐾</span>
                        <p style="margin:.4rem 0 0; font-size:.85rem; color:var(--text-muted)">Cliquez pour ajouter une photo</p>
                    </div>
                </div>
                <input type="file" id="animalPhotoInput" name="animal_photo"
                       accept="image/jpeg,image/png,image/webp,image/gif" style="display:none">
            </div>
            <div class="form-group">
                <label>Message (optionnel)</label>
                <textarea name="message" rows="3" placeholder="Présentez votre animal, ses habitudes..."></textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-ghost" onclick="closeReservation()">Annuler</button>
                <button type="submit" class="btn btn-primary">Envoyer ma demande</button>
            </div>
        </form>
    </div>
</div>
<script>
// ── Données créneaux ──────────────────────────────────────────────
const creneaux = <?= json_encode($creneauxJS ?? []) ?>;
const isLoggedParticulier = <?= (!$estLePro && isLoggedOn() && isParticulier()) ? 'true' : 'false' ?>;
const isLoggedIn = <?= isLoggedOn() ? 'true' : 'false' ?>;

// ── Modal réservation ─────────────────────────────────────────────
function openReservation(dateDebut, dateFin, labelDebut, labelFin) {
    document.getElementById('hiddenDebut').value = dateDebut;
    document.getElementById('hiddenFin').value   = dateFin;
    document.getElementById('modalDates').textContent = 'Du ' + labelDebut + ' au ' + labelFin;
    document.getElementById('modalReservation').style.display = 'flex';
}
function closeReservation() {
    document.getElementById('modalReservation').style.display = 'none';
}
document.getElementById('modalReservation').addEventListener('click', function(e) {
    if (e.target === this) closeReservation();
});

// ── Calendrier avec sélection de plage ───────────────────────────
(function() {
    const moisFR = ['Janvier','Février','Mars','Avril','Mai','Juin',
                    'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

    // Construire une map dateStr -> { hasDispo, hasReserve, placesLibres, capaciteMax }
    const dateMap = {};
    creneaux.forEach(c => {
        let cur = new Date(c.dateDebut + 'T00:00:00');
        const fin = new Date(c.dateFin + 'T00:00:00');
        while (cur <= fin) {
            const key = cur.toISOString().slice(0,10);
            if (!dateMap[key]) dateMap[key] = { hasDispo: false, hasReserve: false, placesLibres: 0, capaciteMax: c.capaciteMax };
            if (c.statut === 'disponible' && c.placesLibres > 0) {
                dateMap[key].hasDispo = true;
                dateMap[key].placesLibres = Math.max(dateMap[key].placesLibres, c.placesLibres);
                dateMap[key].capaciteMax  = c.capaciteMax;
            } else {
                dateMap[key].hasReserve = true;
            }
            cur.setDate(cur.getDate() + 1);
        }
    });

    // Trouver le premier mois avec des créneaux
    const today = new Date().toISOString().slice(0,10);
    const firstFuture = creneaux.find(c => c.dateFin >= today);
    const refDate = firstFuture ? new Date(firstFuture.debut) : new Date();
    let viewYear  = refDate.getFullYear();
    let viewMonth = refDate.getMonth();

    // État sélection plage
    let rangeStart = null;
    let rangeEnd   = null;

    function dateStr(y, m, d) {
        return y + '-' + String(m+1).padStart(2,'0') + '-' + String(d).padStart(2,'0');
    }
    function fmtLabel(ds) {
        const [y,m,d] = ds.split('-');
        return d + '/' + m + '/' + y;
    }

    // Vérifie qu'une date est disponible (au moins un créneau dispo la couvre)
    function isDispo(ds) {
        return dateMap[ds] && dateMap[ds].hasDispo;
    }

    // Vérifie que toute la plage start→end est disponible
    function rangeAllDispo(start, end) {
        let cur = new Date(start + 'T00:00:00');
        const fin = new Date(end   + 'T00:00:00');
        while (cur <= fin) {
            const ds = cur.toISOString().slice(0,10);
            if (!isDispo(ds)) return false;
            cur.setDate(cur.getDate() + 1);
        }
        return true;
    }

    function renderCalendar() {
        document.getElementById('calMonthLabel').textContent = moisFR[viewMonth] + ' ' + viewYear;

        const grid = document.getElementById('calDays');
        grid.innerHTML = '';

        const first = new Date(viewYear, viewMonth, 1);
        let startDow = first.getDay();
        startDow = startDow === 0 ? 6 : startDow - 1;
        const lastDay = new Date(viewYear, viewMonth + 1, 0).getDate();

        for (let i = 0; i < startDow; i++) {
            const empty = document.createElement('div');
            empty.className = 'cal-day cal-day-empty';
            grid.appendChild(empty);
        }

        for (let d = 1; d <= lastDay; d++) {
            const ds   = dateStr(viewYear, viewMonth, d);
            const cell = document.createElement('div');
            cell.className = 'cal-day';
            cell.textContent = d;

            const info = dateMap[ds];

            if (ds < today) {
                cell.classList.add('cal-day-past');
            } else if (info && info.hasDispo) {
                // Coloration plage sélectionnée
                if (rangeStart && rangeEnd) {
                    if (ds === rangeStart)                       cell.classList.add('cal-day-range-start');
                    else if (ds === rangeEnd)                    cell.classList.add('cal-day-range-end');
                    else if (ds > rangeStart && ds < rangeEnd)  cell.classList.add('cal-day-in-range');
                    else                                         cell.classList.add('cal-day-dispo');
                } else if (rangeStart && ds === rangeStart) {
                    cell.classList.add('cal-day-range-start');
                } else {
                    cell.classList.add('cal-day-dispo');
                }
                // Badge places libres (si capacité > 1)
                if (info.capaciteMax > 1) {
                    const badge = document.createElement('span');
                    badge.className = 'cal-places-badge';
                    badge.textContent = info.placesLibres + '/' + info.capaciteMax;
                    cell.appendChild(badge);
                }
                cell.style.cursor = 'pointer';
                cell.addEventListener('click', () => handleDayClick(ds));
            } else if (info && info.hasReserve) {
                cell.classList.add('cal-day-reserve');
            } else {
                cell.classList.add('cal-day-off');
            }

            grid.appendChild(cell);
        }

        // Afficher/cacher le panneau d'instruction
        updateInstructions();
    }

    function updateInstructions() {
        const box = document.getElementById('calCreneaux');
        const title = document.getElementById('calCreneauxTitle');
        if (!rangeStart) {
            box.style.display = 'block';
            title.textContent = 'Cliquez sur une date de début';
            document.getElementById('calSlotsList').innerHTML = '';
        } else if (!rangeEnd) {
            title.textContent = 'Cliquez sur la date de fin (à partir du ' + fmtLabel(rangeStart) + ')';
        }
    }

    function handleDayClick(ds) {
        if (!isLoggedIn) {
            window.location.href = 'index.php?action=login';
            return;
        }
        if (!isLoggedParticulier) return;

        if (!rangeStart) {
            // Premier clic → début
            rangeStart = ds;
            rangeEnd   = null;
            renderCalendar();
        } else if (!rangeEnd) {
            if (ds < rangeStart) {
                // Clic avant le début → reset et nouveau début
                rangeStart = ds;
                rangeEnd   = null;
                renderCalendar();
                return;
            }
            if (!rangeAllDispo(rangeStart, ds)) {
                showRangeError('Certains jours de cette période ne sont pas disponibles. Choisissez une plage entièrement verte.');
                return;
            }
            rangeEnd = ds;
            renderCalendar();
            // Ouvrir le modal
            openReservation(rangeStart, rangeEnd, fmtLabel(rangeStart), fmtLabel(rangeEnd));
        } else {
            // Reset → nouveau début
            rangeStart = ds;
            rangeEnd   = null;
            renderCalendar();
        }
    }

    function showRangeError(msg) {
        const list = document.getElementById('calSlotsList');
        list.innerHTML = '<p style="color:#dc2626;font-size:.85rem;margin:0">' + msg + '</p>';
        document.getElementById('calCreneaux').style.display = 'block';
    }

    document.getElementById('calPrev').addEventListener('click', () => {
        viewMonth--; if (viewMonth < 0) { viewMonth = 11; viewYear--; }
        renderCalendar();
    });
    document.getElementById('calNext').addEventListener('click', () => {
        viewMonth++; if (viewMonth > 11) { viewMonth = 0; viewYear++; }
        renderCalendar();
    });

    // Bouton reset sélection
    document.getElementById('calResetBtn')?.addEventListener('click', () => {
        rangeStart = null; rangeEnd = null;
        renderCalendar();
    });

    renderCalendar();
})();

/* ── Aperçu photo animal ─────────────────────────────────────────── */
document.getElementById('animalPhotoInput')?.addEventListener('change', function () {
    if (!this.files || !this.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview     = document.getElementById('photoPreview');
        const placeholder = document.getElementById('photoPlaceholder');
        preview.src             = e.target.result;
        preview.style.display   = 'block';
        placeholder.style.display = 'none';
    };
    reader.readAsDataURL(this.files[0]);
});
</script>
