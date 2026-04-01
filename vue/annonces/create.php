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
$animauxActifs = $profilPro ? explode(',', $profilPro['animaux_acceptes']) : ['chien'];
?>
<div class="page-header">
    <div class="container">
        <h1>Mon profil professionnel</h1>
        <p>Complétez votre profil pour être visible par les propriétaires</p>
    </div>
</div>

<div class="container">
    <div class="form-layout">
        <form method="POST" action="index.php?action=creerAnnonce" enctype="multipart/form-data" class="main-form">

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="form-section">
                <h2 class="form-section-title">Mon établissement</h2>

                <div class="form-group">
                    <label>Nom de la structure (optionnel)</label>
                    <input type="text" name="nom_structure" placeholder="Ex: Happy Pets, Tom Pet Care..."
                           value="<?= htmlspecialchars($profilPro['nom_structure'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Photo annonce proffessionnelle</label>

                    <!-- Zone de dépôt -->
                    <div class="photo-upload" id="photoUpload">
                        <input type="file" id="pro_photo_file"
                               accept="image/jpeg,image/png,image/webp" class="photo-input">
                        <!-- Champ caché qui reçoit le base64 recadré -->
                        <input type="hidden" name="pro_photo_cropped" id="pro_photo_cropped">

                        <div class="photo-placeholder" id="photoPlaceholder">
                            <?php if (!empty($profilPro['photo'])): ?>
                                <img src="<?= BASE_URL ?>/images/uploads/<?= htmlspecialchars($profilPro['photo']) ?>"
                                     alt="Photo actuelle"
                                     style="width:100px;height:100px;object-fit:cover;border-radius:50%;margin-bottom:.5rem">
                                <span>Changer la photo</span>
                            <?php else: ?>
                                <span class="photo-icon"><img src="<?= BASE_URL ?>/images/icons/photo.png" alt="photo"></span>
                                <span>Cliquez pour ajouter une photo</span>
                                <small>JPG, PNG ou WEBP — Max 5 Mo</small>
                            <?php endif; ?>
                        </div>

                        <!-- Prévisualisation finale (cercle) -->
                        <div id="cropResult" style="display:none;flex-direction:column;align-items:center;gap:.75rem">
                            <img id="cropPreview"
                                 style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid var(--primary)">
                            <button type="button" class="btn btn-ghost btn-sm" id="btnChangePic">Changer</button>
                        </div>
                    </div>

                    <!-- Modal recadrage -->
                    <div class="modal-overlay" id="cropModal" style="display:none">
                        <div class="modal-box" style="max-width:520px">
                            <div class="modal-header">
                                <h3>Recadrer la photo</h3>
                                <button type="button" class="modal-close" id="btnCancelCrop">✕</button>
                            </div>
                            <div style="position:relative;width:100%;background:#111;border-radius:var(--radius);overflow:hidden;touch-action:none">
                                <canvas id="cropCanvas" style="display:block;max-width:100%;cursor:move"></canvas>
                                <!-- cercle de masque SVG -->
                                <svg id="cropMask" style="position:absolute;inset:0;width:100%;height:100%;pointer-events:none">
                                    <defs>
                                        <mask id="hole">
                                            <rect width="100%" height="100%" fill="white"/>
                                            <circle id="maskCircle" fill="black"/>
                                        </mask>
                                    </defs>
                                    <rect width="100%" height="100%" fill="rgba(0,0,0,.55)" mask="url(#hole)"/>
                                    <circle id="maskBorder" fill="none" stroke="white" stroke-width="2"/>
                                </svg>
                            </div>
                            <p style="font-size:.82rem;color:var(--text-muted);margin:.6rem 0 1rem;text-align:center">
                                Glissez pour déplacer · Molette ou pincement pour zoomer
                            </p>
                            <div class="form-actions">
                                <button type="button" class="btn btn-ghost" id="btnCancelCrop2">Annuler</button>
                                <button type="button" class="btn btn-primary" id="btnApplyCrop">✓ Appliquer</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Adresse <span class="req">*</span></label>
                        <input type="text" name="adresse" required placeholder="12 rue des Fleurs"
                               value="<?= htmlspecialchars($profilPro['adresse'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Ville <span class="req">*</span></label>
                        <input type="text" name="ville" required placeholder="Paris"
                               value="<?= htmlspecialchars($profilPro['ville'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Code postal</label>
                        <input type="text" name="code_postal" placeholder="75011"
                               value="<?= htmlspecialchars($profilPro['code_postal'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Capacité maximale d'accueil</label>
                        <input type="number" name="capacite_max" min="1" max="20"
                               value="<?= htmlspecialchars($profilPro['capacite_max'] ?? 1) ?>">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="form-section-title">Animaux acceptés</h2>
                <div class="checkboxes-grid">
                    <?php foreach ($typeImages as $val => $emoji): ?>
                    <label class="checkbox-card <?= in_array($val, $animauxActifs) ? 'checked' : '' ?>">
                        <input type="checkbox" name="animaux_acceptes[]" value="<?= $val ?>"
                               <?= in_array($val, $animauxActifs) ? 'checked' : '' ?>
                               onchange="this.closest('label').classList.toggle('checked', this.checked)">
                        <span><img src="<?= $emoji ?>" class="animal-img-icon" alt="<?= ucfirst($val) ?>"></span>
                        <span><?= ucfirst($val) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-section">
                <h2 class="form-section-title">Mes tarifs (€/heure)</h2>
                <p class="form-hint">Définissez un prix par type d'animal que vous acceptez.</p>
                <div class="tarifs-form-grid">
                    <?php foreach ($typeImages as $val => $emoji): ?>
                    <div class="form-group">
                        <label><img src="<?= $emoji ?>" class="animal-img-icon" alt="<?= ucfirst($val) ?>"> <?= ucfirst($val) ?></label>
                        <div class="input-prefix">
                            <span>€</span>
                            <input type="number" name="tarifs[<?= $val ?>]" min="0.5" step="0.5" placeholder="—"
                                   value="<?= isset($tarifs[$val]) ? number_format($tarifs[$val], 2, '.', '') : '' ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php?action=profil" class="btn btn-ghost">Annuler</a>
                <button type="submit" class="btn btn-primary btn-lg">Sauvegarder mon profil</button>
            </div>
        </form>

        <div class="form-sidebar">
            <div class="tips-card">
                <h3>Conseils</h3>
                <ul>
                    <li>Un profil complet reçoit 3x plus de demandes</li>
                    <li>Précisez votre adresse pour apparaître dans les recherches</li>
                    <li>Définissez des tarifs clairs pour chaque animal</li>
                    <li>Ajoutez vos créneaux depuis votre tableau de bord</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
/* ── Recadrage photo pro (format 4/3 rectangulaire) ─────────── */
(function () {
    const fileInput    = document.getElementById('pro_photo_file');
    const croppedInput = document.getElementById('pro_photo_cropped');
    const placeholder  = document.getElementById('photoPlaceholder');
    const cropResult   = document.getElementById('cropResult');
    const cropPreview  = document.getElementById('cropPreview');
    const cropModal    = document.getElementById('cropModal');
    const canvas       = document.getElementById('cropCanvas');
    const ctx          = canvas.getContext('2d');

    /* Supprimer le masque SVG circulaire */
    const svg = document.getElementById('cropMask');
    if (svg) svg.remove();

    document.getElementById('photoUpload').addEventListener('click', function(e) {
        if (!e.target.closest('#cropResult') && !e.target.closest('.modal-overlay'))
            fileInput.click();
    });
    const btnChange = document.getElementById('btnChangePic');
    if (btnChange) btnChange.addEventListener('click', () => fileInput.click());

    let img, ox = 0, oy = 0, scale = 1, minScale = 1;
    const RATIO  = 4 / 3;
    const SIZE   = 460;
    const CROP_H = Math.round(SIZE / RATIO);

    fileInput.addEventListener('change', function () {
        if (!this.files || !this.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => openCrop(e.target.result);
        reader.readAsDataURL(this.files[0]);
        this.value = '';
    });

    function openCrop(src) {
        img = new Image();
        img.onload = function () {
            canvas.width  = SIZE;
            canvas.height = CROP_H;
            const scaleW = SIZE   / img.naturalWidth;
            const scaleH = CROP_H / img.naturalHeight;
            minScale = Math.max(scaleW, scaleH);
            scale    = minScale;
            ox = (SIZE   - img.naturalWidth  * scale) / 2;
            oy = (CROP_H - img.naturalHeight * scale) / 2;
            cropModal.style.display = 'flex';
            draw();
        };
        img.src = src;
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, ox, oy, img.naturalWidth * scale, img.naturalHeight * scale);
        ctx.strokeStyle = 'rgba(255,255,255,.25)';
        ctx.lineWidth   = 1;
        for (let i = 1; i < 3; i++) {
            ctx.beginPath(); ctx.moveTo(SIZE * i / 3, 0); ctx.lineTo(SIZE * i / 3, CROP_H); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(0, CROP_H * i / 3); ctx.lineTo(SIZE, CROP_H * i / 3); ctx.stroke();
        }
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
            const d=Math.hypot(e.touches[0].clientX-e.touches[1].clientX,e.touches[0].clientY-e.touches[1].clientY);
            if (lastDist) applyZoom((d-lastDist)*0.005,SIZE/2,CROP_H/2);
            lastDist=d;
        } else if (e.touches.length===1&&dragging) { ox=sox+(e.touches[0].clientX-sx); oy=soy+(e.touches[0].clientY-sy); clamp(); draw(); }
    }, {passive:false});
    canvas.addEventListener('touchend', () => { dragging=false; lastDist=null; });
    canvas.addEventListener('wheel', e => { e.preventDefault(); applyZoom(-e.deltaY*0.001,e.offsetX,e.offsetY); }, {passive:false});

    function applyZoom(delta, px, py) {
        const ns=Math.max(minScale,Math.min(scale*(1+delta),minScale*6));
        const r=ns/scale;
        ox=px-(px-ox)*r; oy=py-(py-oy)*r; scale=ns; clamp(); draw();
    }

    function clamp() {
        const w=img.naturalWidth*scale, h=img.naturalHeight*scale;
        if (ox>0) ox=0; if (ox+w<SIZE) ox=SIZE-w;
        if (oy>0) oy=0; if (oy+h<CROP_H) oy=CROP_H-h;
    }

    document.getElementById('btnApplyCrop').addEventListener('click', () => {
        const OUT_W=800, OUT_H=Math.round(OUT_W/RATIO);
        const fc=document.createElement('canvas'); fc.width=OUT_W; fc.height=OUT_H;
        const fctx=fc.getContext('2d');
        const srcX=-ox/scale, srcY=-oy/scale, srcW=SIZE/scale, srcH=CROP_H/scale;
        fctx.drawImage(img,srcX,srcY,srcW,srcH,0,0,OUT_W,OUT_H);
        const dataURL=fc.toDataURL('image/jpeg',0.92);
        croppedInput.value=dataURL;
        cropPreview.src=dataURL;
        cropPreview.style.borderRadius='8px';
        cropPreview.style.width='100%';
        cropPreview.style.height='auto';
        cropModal.style.display='none';
        placeholder.style.display='none';
        cropResult.style.display='flex';
    });

    function cancelCrop() { cropModal.style.display='none'; }
    document.getElementById('btnCancelCrop').addEventListener('click',  cancelCrop);
    document.getElementById('btnCancelCrop2').addEventListener('click', cancelCrop);
    cropModal.addEventListener('click', e => { if (e.target===cropModal) cancelCrop(); });
})();
</script>