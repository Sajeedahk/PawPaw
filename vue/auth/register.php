<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Créer un compte</h1>
            <p>Rejoignez la communauté Paw Paw</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=inscription" class="auth-form">

            <div class="form-group">
                <label>Je suis… <span class="req">*</span></label>
                <div class="role-selector">
                    <label class="role-card <?= (($_POST['role'] ?? 'particulier') === 'particulier') ? 'active' : '' ?>">
                        <input type="radio" name="role" value="particulier"
                               <?= (($_POST['role'] ?? 'particulier') === 'particulier') ? 'checked' : '' ?>
                               onchange="document.querySelectorAll('.role-card').forEach(c=>c.classList.remove('active')); this.closest('.role-card').classList.add('active')">
                        <span class="role-emoji"><img src="<?= BASE_URL ?>/images/icons/proprio.png" alt="logo"></span> <br>
                        <strong>Propriétaire</strong>
                        <small>Je cherche un un pet sitterpour mon animal</small>
                    </label>
                    <label class="role-card <?= (($_POST['role'] ?? '') === 'pro') ? 'active' : '' ?>">
                        <input type="radio" name="role" value="pro"
                               <?= (($_POST['role'] ?? '') === 'pro') ? 'checked' : '' ?>
                               onchange="document.querySelectorAll('.role-card').forEach(c=>c.classList.remove('active')); this.closest('.role-card').classList.add('active')">
                        <span class="role-emoji2"><img src="<?= BASE_URL ?>/images/icons/petSitter.png" alt="logo"></span>
                        <br>
                        
                        <strong>Professionnel</strong>
                        <small>Je propose mes services de garde</small>
                    </label>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="prenom">Prénom <span class="req">*</span></label>
                    <input type="text" id="prenom" name="prenom" required placeholder="Marie"
                           value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="nom">Nom <span class="req">*</span></label>
                    <input type="text" id="nom" name="nom" required placeholder="Dupont"
                           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email <span class="req">*</span></label>
                <input type="email" id="email" name="email" required placeholder="vous@exemple.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" placeholder="06 12 34 56 78"
                       value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe <span class="req">*</span></label>
                <div class="input-password">
                    <input type="password" id="password" name="password" required
                           placeholder="Min. 12 caractères" autocomplete="new-password"
                           oninput="evalPassword(this.value)">
                    <button type="button" class="toggle-pwd" onclick="togglePwd('password')" tabindex="-1">👁</button>
                </div>
                <!-- Jauge de force -->
                <div class="pwd-strength-bar" id="pwdBar">
                    <div class="pwd-strength-fill" id="pwdFill"></div>
                </div>
                <p class="pwd-strength-label" id="pwdLabel"></p>
                <!-- Règles visuelles -->
                <ul class="pwd-rules" id="pwdRules">
                    <li id="rLen">✗ Au moins 12 caractères</li>
                    <li id="rUp">✗ Une lettre majuscule</li>
                    <li id="rNum">✗ Un chiffre</li>
                    <li id="rSpec">✗ Un caractère spécial (!@#$…)</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe <span class="req">*</span></label>
                <div class="input-password">
                    <input type="password" id="password_confirm" name="password_confirm" required
                           placeholder="Répétez votre mot de passe" autocomplete="new-password"
                           oninput="checkConfirm()">
                    <button type="button" class="toggle-pwd" onclick="togglePwd('password_confirm')" tabindex="-1">👁</button>
                </div>
                <p class="pwd-confirm-msg" id="pwdConfirmMsg"></p>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitBtn">Créer mon compte</button>
        </form>

        <div class="auth-footer">
            <p>Déjà membre ? <a href="index.php?action=login">Se connecter</a></p>
        </div>
    </div>
    <div class="auth-illustration">
        <h2>Gardez et faites<br>garder vos animaux</h2>
        <p>Inscription gratuite · Réservation simple · Communauté bienveillante</p>
        <span class="auth-illustration"><img src="<?= BASE_URL ?>/images/images/img2.png" alt="logo"></span>
    </div>
</div>
<script>
/* ── Jauge de force du mot de passe ─────────────────────────────── */
function evalPassword(val) {
    const rules = {
        rLen:  val.length >= 8,
        rUp:   /[A-Z]/.test(val),
        rNum:  /[0-9]/.test(val),
        rSpec: /[^A-Za-z0-9]/.test(val),
    };
    let score = Object.values(rules).filter(Boolean).length;

    // Mise à jour des règles visuelles
    Object.entries(rules).forEach(([id, ok]) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = (ok ? '✓' : '✗') + ' ' + el.textContent.slice(2);
        el.classList.toggle('rule-ok', ok);
        el.classList.toggle('rule-ko', !ok);
    });

    // Jauge
    const fill  = document.getElementById('pwdFill');
    const label = document.getElementById('pwdLabel');
    const levels = [
        { pct: 0,   color: '#e5e7eb', text: '' },
        { pct: 25,  color: '#ef4444', text: 'Très faible' },
        { pct: 50,  color: '#f97316', text: 'Faible' },
        { pct: 75,  color: '#eab308', text: 'Moyen' },
        { pct: 100, color: '#22c55e', text: 'Fort ' },
    ];
    const lvl = val.length === 0 ? levels[0] : levels[score];
    fill.style.width      = lvl.pct + '%';
    fill.style.background = lvl.color;
    label.textContent     = lvl.text;
    label.style.color     = lvl.color;

    checkConfirm();
    updateSubmit(score, rules);
}

function checkConfirm() {
    const pwd  = document.getElementById('password').value;
    const conf = document.getElementById('password_confirm').value;
    const msg  = document.getElementById('pwdConfirmMsg');
    if (!conf) { msg.textContent = ''; return; }
    if (pwd === conf) {
        msg.textContent = 'Les mots de passe correspondent';
        msg.style.color = '#22c55e';
    } else {
        msg.textContent = 'Les mots de passe ne correspondent pas';
        msg.style.color = '#ef4444';
    }
}

function updateSubmit(score, rules) {
    const btn = document.getElementById('submitBtn');
    if (!btn) return;
    // On bloque si le mdp est trop court (règle minimale)
    btn.disabled = !rules.rLen;
    btn.style.opacity = rules.rLen ? '1' : '0.55';
}

/* Empêcher la soumission si les mdp ne correspondent pas */
document.querySelector('.auth-form').addEventListener('submit', function(e) {
    const pwd  = document.getElementById('password').value;
    const conf = document.getElementById('password_confirm').value;
    if (pwd !== conf) {
        e.preventDefault();
        document.getElementById('pwdConfirmMsg').textContent = '✗ Les mots de passe ne correspondent pas';
        document.getElementById('pwdConfirmMsg').style.color = '#ef4444';
        document.getElementById('password_confirm').focus();
    }
    if (pwd.length < 8) {
        e.preventDefault();
        document.getElementById('password').focus();
    }
});
</script>
