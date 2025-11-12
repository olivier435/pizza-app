<?php

/** @var string $selector */
/** @var string $token */
?>
<section class="inner-hero section dark-background"></section>
<section class="section login-register auth" id="reset-password">
  <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8">
        <div class="login-register-wraper">
          <h2 class="mb-4 text-center">Nouveau mot de passe</h2>
          <form action="/forgot-password/<?= htmlspecialchars($selector, ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>" method="post">
            <div class="row g-3">
              <div class="col-12">
                <div class="mb-4">
                  <label for="password" class="form-label">Mot de passe (min. 8)</label>
                  <div class="input-group mb-2">
                    <input id="password" name="password" type="password" minlength="8" required class="form-control" autocomplete="new-password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Afficher le mot de passe">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-outline-primary" type="button" id="generate-password" title="Générer un mot de passe fort">
                      <i class="bi bi-shuffle"></i> <span>Générer</span>
                    </button>
                  </div>
                  <span id="entropy" class="badge text-bg-secondary">Très faible</span>
                  <div class="progress mt-2" style="height: 6px;">
                    <div id="password-progress" class="progress-bar bg-danger" role="progressbar" style="width: 5%;"></div>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <div class="mb-4">
                  <label for="password2" class="form-label">Confirmer le mot de passe</label>
                  <input id="password2" name="password2" type="password" minlength="8" required class="form-control" autocomplete="new-password">
                </div>
              </div>
              <div class="col-12 d-grid">
                <button type="submit" class="btn btn-primary" id="submit-button">Enregistrer</button>
              </div>
              <div class="col-12 text-center">
                <p class="mt-3 mb-0">
                  <a href="/login">Retour à la connexion</a>
                </p>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<script type="module" src="/assets/js/register.js"></script>