<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Connexion</h1>
            <p>Bon retour sur Paw Paw !</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=login" class="auth-form">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" required
                       placeholder="vous@exemple.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="input-password">
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                    <button type="button" class="toggle-pwd" onclick="togglePwd('password')">👁</button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Se connecter</button>
        </form>

        <div class="auth-footer">
            <p>Pas encore de compte ? <a href="index.php?action=inscription">S'inscrire gratuitement</a></p>
        </div>
    </div>
    <div class="auth-illustration">
        <h2>La garde d'animaux<br>par des professionnels</h2>
        <p>Rejoignez des centaines de propriétaires et gardiens bienveillants</p>
        <span class="auth-illustration2"><img src="<?= BASE_URL ?>/images/images/img4.png" alt="logo"></span>
    </div>
</div>
