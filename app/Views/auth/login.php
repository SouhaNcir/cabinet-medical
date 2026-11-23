<section class="login-layout">
    <aside class="login-aside">
        <div class="login-brand">
            <div class="login-brand-icon"><i class="fa-solid fa-stethoscope"></i></div>
            <div>
                <h1>Cabinet Medical</h1>
                <p>Gestion medicale premium</p>
            </div>
        </div>

        <div class="login-aside-content">
            <h2>Gerez votre cabinet avec precision</h2>
            <p>Interface clinique pensee pour les medecins et secretaires au quotidien.</p>

            <div class="login-features">
                <article class="login-feature">
                    <span class="login-feature-icon"><i class="fa-solid fa-shield-halved"></i></span>
                    <div>
                        <strong>Securite renforcee</strong>
                        <p>Sessions, CSRF et acces par role</p>
                    </div>
                </article>

                <article class="login-feature">
                    <span class="login-feature-icon"><i class="fa-solid fa-chart-bar"></i></span>
                    <div>
                        <strong>Vision instantanee</strong>
                        <p>Tableau de bord et indicateurs cles</p>
                    </div>
                </article>

                <article class="login-feature">
                    <span class="login-feature-icon"><i class="fa-regular fa-calendar"></i></span>
                    <div>
                        <strong>Flux medical fluide</strong>
                        <p>Rendez-vous, dossiers et ordonnances</p>
                    </div>
                </article>
            </div>
        </div>

        <div class="login-uptime">
            <span class="dot"></span>
            Systeme operationnel - 99.9% uptime
        </div>
    </aside>

    <main class="login-main">
        <div class="login-form-wrap">
            <div class="login-top">
                <span class="login-security-badge"><i class="fa-solid fa-lock"></i> Connexion securisee</span>
                <h2>Bienvenue</h2>
                <p>Connectez-vous a votre espace de gestion.</p>
            </div>

            <?php if (!empty($flash)): ?>
                <div class="notification notification-<?= e($flash['type']) ?>">
                    <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                    <span><?= e($flash['message']) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(BASE_URL) ?>/auth/authenticate" class="login-form">
                <input type="hidden" name="_token" value="<?= e($csrf ?? '') ?>">

                <label for="email">Email professionnel</label>
                <div class="login-input-wrap">
                    <i class="fa-regular fa-envelope login-input-icon"></i>
                    <input id="email" type="email" name="email" placeholder="nom@cabinet.ma" autocomplete="email" required>
                </div>

                <label for="password">Mot de passe</label>
                <div class="login-input-wrap">
                    <i class="fa-solid fa-lock login-input-icon"></i>
                    <input id="password" type="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
                    <button type="button" class="password-toggle" id="passwordToggle" aria-label="Afficher le mot de passe">
                        <i class="fa-regular fa-eye" id="passwordToggleIcon"></i>
                    </button>
                </div>

                <div class="login-row">
                    <label class="login-remember">
                        <input type="checkbox" name="remember">
                        <span>Se souvenir de moi</span>
                    </label>
                    <a href="#" class="login-link">Mot de passe oublie ?</a>
                </div>

                <button type="submit" class="login-submit">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Se connecter
                </button>
            </form>
        </div>
    </main>
</section>
