<?php

/** @var string $lastEmail */
?>
<section class="inner-hero section dark-background"></section>
<main class="main">
    <div class="page-auth light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0">Connexion</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="/">Accueil</a></li>
                    <li class="current">Se connecter</li>
                </ol>
            </nav>
        </div>
    </div>
    <?php include __DIR__ . '/../layout/_flash.php'; ?>
    <section class="section login-register auth">
        <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="login-register-wraper">
                        <ul class="nav nav-tabs nav-tabs-bordered justify-content-center mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#login-register-login-form" type="button" role="tab" aria-selected="true">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Connexion
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="login-register-login-form" role="tabpanel">
                                <form method="post" action="/login">
                                    <div class="mb-4">
                                        <label for="email" class="form-label">Email</label>
                                        <input id="email" name="email" type="email" required class="form-control" value="<?= htmlspecialchars($lastEmail ?? '') ?>">
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label">Mot de passe</label>
                                        <div class="input-group">
                                            <input
                                                id="password"
                                                name="password"
                                                type="password"
                                                required
                                                class="form-control"
                                                autocomplete="current-password">
                                            <button
                                                class="btn btn-outline-secondary"
                                                type="button"
                                                id="togglePassword"
                                                aria-label="Afficher le mot de passe"
                                                aria-pressed="false">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <div class="d-flex align-items-center">
                                                      <input type="checkbox" class="form-check-input" id="login-register-remember-me" name="remember">
                                                <label class="form-check-label" for="login-register-remember-me">
                                                    Se souvenir de moi
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-primary btn-lg" type="submit">Se connecter</button>
                                    </div>
                                    <p class="mt-3 mb-0">Pas encore de compte ? <a href="/register">S'inscrire</a></p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pwd = document.getElementById('password');
        const btn = document.getElementById('togglePassword');
        if (!pwd || !btn) return;

        btn.addEventListener('click', () => {
            const show = pwd.type === 'password';
            pwd.type = show ? 'text' : 'password';

            btn.setAttribute('aria-pressed', show ? 'true' : 'false');
            btn.setAttribute('aria-label', show ? 'Masquer le mot de passe' : 'Afficher le mot de passe');

            const icon = btn.querySelector('i');
            if (icon) icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
</script>