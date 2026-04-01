<?php
$typeEmojis = ['chien'=>'','chat'=>'','lapin'=>'','oiseau'=>'','rongeur'=>'','reptile'=>'','poisson'=>'','autre'=>''];
?>
<div class="page-header">
    <div class="container">
        <div class="profile-header">
            <!-- Avatar cliquable -->
            <div class="profile-avatar-lg avatar-editable" id="avatarTrigger" title="Changer ma photo">
                <?php if (!empty($_SESSION['user_photo'])): ?>
                    <img src="<?= BASE_URL ?>/images/uploads/<?= htmlspecialchars($_SESSION['user_photo']) ?>"
                         alt="Photo de profil"
                         style="width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;">
                <?php else: ?>
                    <?= mb_strtoupper(mb_substr($user['prenom'], 0, 1)) ?>
                <?php endif; ?>
                <div class="avatar-edit-overlay">📷</div>
            </div>
            <div>
                <h1><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h1>
                <p><?= $user['role'] === 'pro' ? 'Gardien professionnel' : 'Propriétaire' ?>
                   · Membre depuis le <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Input fichier caché -->
<input type="file" id="avatarFileInput" accept="image/jpeg,image/png,image/webp" style="display:none">

<!-- Modal recadrage avatar -->
<div class="modal-overlay" id="avatarCropModal" style="display:none">
    <div class="modal-box" style="max-width:520px">
        <div class="modal-header">
            <h3>📷 Ma photo de profil</h3>
            <button type="button" class="modal-close" id="btnAvatarCancel">✕</button>
        </div>
        <div style="position:relative;width:100%;background:#111;border-radius:var(--radius);overflow:hidden;touch-action:none">
            <canvas id="avatarCanvas" style="display:block;max-width:100%;cursor:move"></canvas>
            <svg id="avatarMask" style="position:absolute;inset:0;width:100%;height:100%;pointer-events:none">
                <defs>
                    <mask id="avatarHole">
                        <rect width="100%" height="100%" fill="white"/>
                        <circle id="avatarMaskCircle" fill="black"/>
                    </mask>
                </defs>
                <rect width="100%" height="100%" fill="rgba(0,0,0,.55)" mask="url(#avatarHole)"/>
                <circle id="avatarMaskBorder" fill="none" stroke="white" stroke-width="2"/>
            </svg>
        </div>
        <p style="font-size:.82rem;color:var(--text-muted);margin:.6rem 0 1rem;text-align:center">
            Glissez pour déplacer · Molette ou pincement pour zoomer
        </p>
        <div class="form-actions">
            <button type="button" class="btn btn-ghost" id="btnAvatarCancel2">Annuler</button>
            <button type="button" class="btn btn-primary" id="btnAvatarApply">✓ Appliquer</button>
        </div>
    </div>
</div>

<!-- Formulaire silencieux pour envoyer le base64 -->
<form method="POST" action="index.php?action=updateAvatar" id="avatarForm">
    <input type="hidden" name="avatar_cropped" id="avatarCropped">
</form>

<div class="container">
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isPro()): ?>
    <!-- ══════════════ VUE PRO ══════════════ -->
    <div class="profile-layout">

        <!-- Créneaux -->
        <section class="profile-section">
            <div class="section-head">
                <h2>Mes créneaux</h2>
                <a href="index.php?action=annonce&id=<?= $user['id'] ?>" class="btn btn-outline btn-sm">Voir mon profil public</a>
            </div>

            <form method="POST" action="index.php?action=modifierAnnonce" class="creneau-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Début <span class="req">*</span></label>
                        <input type="datetime-local" name="date_debut" required>
                    </div>
                    <div class="form-group">
                        <label>Fin <span class="req">*</span></label>
                        <input type="datetime-local" name="date_fin" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">+ Ajouter ce créneau</button>
            </form>

            <?php if (empty($creneaux)): ?>
                <div class="empty-state-sm"><p>Aucun créneau. Ajoutez vos disponibilités ci-dessus.</p></div>
            <?php else: ?>
                <div class="creneaux-admin-list">
                    <?php foreach ($creneaux as $c): ?>
                    <div class="creneau-admin-row">
                        <div>
                            <strong><?= date('d/m/Y H:i', strtotime($c['date_debut'])) ?></strong>
                            → <strong><?= date('d/m/Y H:i', strtotime($c['date_fin'])) ?></strong>
                        </div>
                        <span class="card-status <?= $c['statut'] === 'disponible' ? 'status-available' : 'status-reserved' ?>">
                            <?= $c['statut'] === 'disponible' ? 'Disponible' : 'Réservé' ?>
                        </span>
                        <?php if ($c['statut'] === 'disponible'): ?>
                        <form method="POST" action="index.php?action=supprimerAnnonce&id=<?= $c['id'] ?>"
                              onsubmit="return confirm('Supprimer ce créneau ?')">
                            <button class="btn btn-ghost btn-sm btn-danger-ghost">🗑</button>
                        </form>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Demandes reçues -->
        <section class="profile-section">
            <div class="section-head">
                <h2>Demandes de garde (<?= count($reservations) ?>)</h2>
            </div>
            <?php if (empty($reservations)): ?>
                <div class="empty-state-sm"><p>Aucune demande reçue pour le moment.</p></div>
            <?php else: ?>
                <div class="profile-annonces-list">
                    <?php foreach ($reservations as $r):
                        $statutLabels  = ['en_attente'=>'En attente','confirme'=>'Confirmé','refuse'=>'Refusé','annule'=>'Annulé'];
                        $statutClasses = ['en_attente'=>'status-pending','confirme'=>'status-available','refuse'=>'status-done','annule'=>'status-done'];
                    ?>
                    <div class="profile-annonce-row">
                        <div class="pa-photo">
                            <?php if (!empty($r['animal_photo'])): ?>
                                <img src="<?= BASE_URL ?>/images/uploads/<?= htmlspecialchars($r['animal_photo']) ?>"
                                     alt="<?= htmlspecialchars($r['animal_nom']) ?>"
                                     class="animal-reservation-photo"
                                     onclick="openAnimalPhoto('<?= BASE_URL ?>/images/uploads/<?= htmlspecialchars($r['animal_photo']) ?>', '<?= htmlspecialchars(addslashes($r['animal_nom'])) ?>')"
                                     title="Voir la photo de <?= htmlspecialchars($r['animal_nom']) ?>">
                            <?php else: ?>
                                <span><?= $typeEmojis[$r['animal_type']] ?? '' ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="pa-info">
                            <strong><?= htmlspecialchars($r['animal_nom']) ?> (<?= ucfirst($r['animal_type']) ?>)</strong>
                            <span>Par <?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?></span>
                            <span><?= date('d/m/Y H:i', strtotime($r['date_debut'])) ?> → <?= date('d/m/Y H:i', strtotime($r['date_fin'])) ?></span>
                            <?php if ($r['message']): ?>
                                <em class="res-message">"<?= htmlspecialchars($r['message']) ?>"</em>
                            <?php endif; ?>
                        </div>
                        <div class="pa-right">
                            <span class="card-status <?= $statutClasses[$r['statut']] ?? '' ?>">
                                <?= $statutLabels[$r['statut']] ?? $r['statut'] ?>
                            </span>
                        </div>
                        <?php if ($r['statut'] === 'en_attente'): ?>
                        <div class="pa-actions">
                            <form method="POST" action="index.php?action=confirmerReservation&id=<?= $r['id'] ?>" style="display:inline">
                                <button class="btn btn-sm btn-success">Confirmer</button>
                            </form>
                            <form method="POST" action="index.php?action=refuserReservation&id=<?= $r['id'] ?>" style="display:inline">
                                <button class="btn btn-sm btn-danger">Refuser</button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="profile-section">
            <div class="section-head"><h2>Mon profil</h2></div>
            <a href="index.php?action=creerAnnonce" class="btn btn-primary">Modifier mon profil pro</a>
        </section>

    </div>

    <?php else: ?>
    <!-- ══════════════ VUE PARTICULIER ══════════════ -->
    <div class="profile-layout">
        <section class="profile-section">
            <div class="section-head">
                <h2>Mes réservations (<?= count($reservations) ?>)</h2>
                <a href="index.php?action=annonces" class="btn btn-primary btn-sm">Trouver un pet sitter</a>
            </div>
            <?php if (empty($reservations)): ?>
                <div class="empty-state-sm">
                    <p>Vous n'avez pas encore effectué de réservation.</p>
                    <a href="index.php?action=annonces" class="btn btn-outline">Voir les pet sitter</a>
                </div>
            <?php else: ?>
                <div class="profile-annonces-list">
                    <?php foreach ($reservations as $r):
                        $statutLabels  = ['en_attente'=>'En attente','confirme'=>'Confirmé','refuse'=>'Refusé','annule'=>'Annulé'];
                        $statutClasses = ['en_attente'=>'status-pending','confirme'=>'status-available','refuse'=>'status-done','annule'=>'status-done'];
                    ?>
                    <div class="profile-annonce-row">
                        <div class="pa-info">
                            <a href="index.php?action=annonce&id=<?= $r['pro_id'] ?>">
                                <strong><?= htmlspecialchars($r['nom_structure'] ?? ($r['pro_prenom'] . ' ' . $r['pro_nom'])) ?></strong>
                            </a>
                            <span><?= htmlspecialchars($r['animal_nom']) ?> · <?= htmlspecialchars($r['ville'] ?? '') ?></span>
                            <span><?= date('d/m/Y', strtotime($r['date_debut'])) ?> → <?= date('d/m/Y', strtotime($r['date_fin'])) ?></span>
                        </div>
                        <div class="pa-right">
                            <span class="card-status <?= $statutClasses[$r['statut']] ?? '' ?>">
                                <?= $statutLabels[$r['statut']] ?? $r['statut'] ?>
                            </span>
                        </div>
                        <?php if ($r['statut'] === 'en_attente'): ?>
                        <div class="pa-actions">
                            <form method="POST" action="index.php?action=annulerReservation&id=<?= $r['id'] ?>"
                                  onsubmit="return confirm('Annuler cette demande ?')">
                                <button class="btn btn-ghost btn-sm btn-danger-ghost">Annuler</button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
    <?php endif; ?>
</div>

<script>
/* ── Crop avatar (commun pro + particulier) ───────────── */
(function () {
    const trigger    = document.getElementById('avatarTrigger');
    const fileInput  = document.getElementById('avatarFileInput');
    const modal      = document.getElementById('avatarCropModal');
    const canvas     = document.getElementById('avatarCanvas');
    const ctx        = canvas.getContext('2d');
    const maskCircle = document.getElementById('avatarMaskCircle');
    const maskBorder = document.getElementById('avatarMaskBorder');
    const form       = document.getElementById('avatarForm');
    const cropped    = document.getElementById('avatarCropped');

    trigger.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', function () {
        if (!this.files || !this.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => openCrop(e.target.result);
        reader.readAsDataURL(this.files[0]);
        this.value = '';
    });

    let img, ox = 0, oy = 0, scale = 1, minScale = 1;
    const SIZE = 460;

    function openCrop(src) {
        img = new Image();
        img.onload = function () {
            const ratio   = img.naturalHeight / img.naturalWidth;
            canvas.width  = SIZE;
            canvas.height = Math.round(SIZE * ratio);
            const r  = Math.min(canvas.width, canvas.height) / 2 - 8;
            const cx = canvas.width  / 2;
            const cy = canvas.height / 2;
            setMask(cx, cy, r);
            minScale = (r * 2) / Math.min(img.naturalWidth, img.naturalHeight);
            scale    = minScale;
            ox = (canvas.width  - img.naturalWidth  * scale) / 2;
            oy = (canvas.height - img.naturalHeight * scale) / 2;
            modal.style.display = 'flex';
            draw();
        };
        img.src = src;
    }

    function setMask(cx, cy, r) {
        [maskCircle, maskBorder].forEach(el => {
            el.setAttribute('cx', cx);
            el.setAttribute('cy', cy);
            el.setAttribute('r',  r);
        });
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, ox, oy, img.naturalWidth * scale, img.naturalHeight * scale);
    }

    let dragging = false, sx, sy, sox, soy;
    canvas.addEventListener('mousedown', e => { dragging=true; sx=e.clientX; sy=e.clientY; sox=ox; soy=oy; });
    window.addEventListener('mousemove', e => { if (!dragging) return; ox=sox+(e.clientX-sx); oy=soy+(e.clientY-sy); clamp(); draw(); });
    window.addEventListener('mouseup',   () => { dragging=false; });

    let lastDist = null;
    canvas.addEventListener('touchstart', e => {
        if (e.touches.length===1) { dragging=true; sx=e.touches[0].clientX; sy=e.touches[0].clientY; sox=ox; soy=oy; }
        lastDist=null;
    }, {passive:true});
    canvas.addEventListener('touchmove', e => {
        e.preventDefault();
        if (e.touches.length===2) {
            const d = Math.hypot(e.touches[0].clientX-e.touches[1].clientX, e.touches[0].clientY-e.touches[1].clientY);
            if (lastDist) applyZoom((d-lastDist)*0.005, canvas.width/2, canvas.height/2);
            lastDist = d;
        } else if (e.touches.length===1 && dragging) {
            ox=sox+(e.touches[0].clientX-sx); oy=soy+(e.touches[0].clientY-sy); clamp(); draw();
        }
    }, {passive:false});
    canvas.addEventListener('touchend', () => { dragging=false; lastDist=null; });
    canvas.addEventListener('wheel', e => { e.preventDefault(); applyZoom(-e.deltaY*0.001, e.offsetX, e.offsetY); }, {passive:false});

    function applyZoom(delta, px, py) {
        const ns = Math.max(minScale, Math.min(scale*(1+delta), minScale*8));
        const r  = ns/scale;
        ox = px-(px-ox)*r; oy = py-(py-oy)*r; scale=ns; clamp(); draw();
    }

    function clamp() {
        const r=Math.min(canvas.width,canvas.height)/2-8, cx=canvas.width/2, cy=canvas.height/2;
        const w=img.naturalWidth*scale, h=img.naturalHeight*scale;
        if (ox>cx-r) ox=cx-r; if (ox+w<cx+r) ox=cx+r-w;
        if (oy>cy-r) oy=cy-r; if (oy+h<cy+r) oy=cy+r-h;
    }

    document.getElementById('btnAvatarApply').addEventListener('click', () => {
        const r=Math.min(canvas.width,canvas.height)/2-8, cx=canvas.width/2, cy=canvas.height/2;
        const OUT=300;
        const fc=document.createElement('canvas'); fc.width=fc.height=OUT;
        const fctx=fc.getContext('2d');
        fctx.beginPath(); fctx.arc(OUT/2,OUT/2,OUT/2,0,Math.PI*2); fctx.clip();
        const srcX=(cx-r-ox)/scale, srcY=(cy-r-oy)/scale, srcW=(r*2)/scale, srcH=(r*2)/scale;
        fctx.drawImage(img,srcX,srcY,srcW,srcH,0,0,OUT,OUT);
        cropped.value = fc.toDataURL('image/jpeg', 0.9);
        modal.style.display = 'none';
        form.submit();
    });

    function cancel() { modal.style.display='none'; }
    document.getElementById('btnAvatarCancel').addEventListener('click',  cancel);
    document.getElementById('btnAvatarCancel2').addEventListener('click', cancel);
    modal.addEventListener('click', e => { if (e.target===modal) cancel(); });
})();

/* ── Lightbox photo animal ───────────────────────────────────────── */
function openAnimalPhoto(src, nom) {
    const lb = document.getElementById('animalPhotoLightbox');
    document.getElementById('lbImg').src = src;
    document.getElementById('lbCaption').textContent = nom;
    lb.style.display = 'flex';
}
document.addEventListener('DOMContentLoaded', () => {
    const lb = document.getElementById('animalPhotoLightbox');
    if (lb) lb.addEventListener('click', e => { if (e.target === lb || e.target.id === 'lbClose') lb.style.display = 'none'; });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && lb) lb.style.display = 'none'; });
});
</script>

<!-- Lightbox photo animal -->
<div id="animalPhotoLightbox" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.82); z-index:9999; align-items:center; justify-content:center; flex-direction:column; gap:1rem;">
    <button id="lbClose" style="position:absolute; top:1.2rem; right:1.5rem; background:none; border:none; color:#fff; font-size:2rem; cursor:pointer; line-height:1;">✕</button>
    <img id="lbImg" src="" alt="" style="max-width:90vw; max-height:78vh; border-radius:12px; object-fit:contain; box-shadow:0 8px 40px rgba(0,0,0,.6);">
    <p id="lbCaption" style="color:#fff; font-size:1.1rem; font-weight:600; margin:0;"></p>
</div>
