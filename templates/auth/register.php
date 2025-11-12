<section class="inner-hero section dark-background"></section>
<main class="main">
    <div class="page-auth light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0">Créer un compte</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="/">Accueil</a></li>
                    <li class="current">Créer un compte</li>
                </ol>
            </nav>
        </div>
    </div>
    <?php include __DIR__ . '/../layout/_flash.php'; ?>
    <section class="section login-register auth">
        <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login-register-wraper">
                        <ul class="nav nav-tabs nav-tabs-bordered justify-content-center mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#login-register-registration-form" type="button" role="tab" aria-selected="true">
                                    <i class="bi bi-person-plus me-1"></i>S'enregistrer
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="login-register-registration-form" role="tabpanel">
                                <form method="post" action="/register">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="mb-4">
                                                <label for="firstname" class="form-label">Prénom</label>
                                                <input id="firstname" name="firstname" type="text" required class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-4">
                                                <label for="lastname" class="form-label">Nom</label>
                                                <input id="lastname" name="lastname" type="text" required class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="email" class="form-label">Email</label>
                                                <input id="email" name="email" type="email" required class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="password" class="form-label">Mot de passe (fort ou très fort)</label>
                                                <div class="input-group mb-2">
                                                    <input id="password" name="password" type="password" minlength="8" required class="form-control">
                                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                                        aria-label="Afficher le mot de passe" aria-pressed="false">
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
                                                <input id="password2" name="password2" type="password" minlength="8" required class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <div class="d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input" id="login-register-terms" name="terms" required="">
                                                    <label class="form-check-label" for="login-register-terms">
                                                        J'accepte les <a href="#">conditions d'utilisation</a> et la <a href="#">politique de confidentialité</a>.
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-grid">
                                                <button class="btn btn-primary btn-lg" type="submit">Créer le compte</button>
                                            </div>
                                        </div>
                                        <p class="mt-3 mb-0">Déjà inscrit ? <a href="/login">Se connecter</a></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script type="module" src="/assets/js/register.js"></script>