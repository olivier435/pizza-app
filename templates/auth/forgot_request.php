<section class="inner-hero section dark-background"></section>
<section class="section login-register forgot" id="forgot-password">
  <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8">
        <div class="login-register-wraper">
          <h2 class="mb-4 text-center">Mot de passe oublié</h2>
          <form action="/forgot-password" method="post" novalidate>
            <div class="row g-3">
              <div class="col-12">
                <div class="mb-4">
                  <label for="email" class="form-label">Votre email</label>
                  <input id="email" name="email" type="email" required class="form-control" autocomplete="email">
                </div>
              </div>
              <div class="col-12 d-grid">
                <button type="submit" class="btn btn-primary">Envoyer le lien de réinitialisation</button>
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