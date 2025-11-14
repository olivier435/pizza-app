<?php
$old    = $old    ?? [];
$errors = $errors ?? [];
?>
<section class="inner-hero section dark-background"></section>
<section id="book-a-table" class="book-a-table section light-background">
    <?php include __DIR__ . '/../layout/_flash.php'; ?>
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row">
            <div class="col-12">
                <div class="reservation-container">
                    <div class="row g-0">
                        <div class="col-lg-5" data-aos="fade-right" data-aos-delay="200">
                            <div class="reservation-form-section">
                                <div class="form-header text-center">
                                    <h3>Réserver une table</h3>
                                    <p>Profitez d'un moment chaleureux autour d'un bon plat. Remplissez le formulaire ci-dessous et nous confirmerons votre réservation au plus vite.</p>
                                </div>
                                <form id="booking-form" action="/booking" method="post" role="form" novalidate>
                                    <div class="row gy-3">
                                        <div class="col-12">
                                            <input
                                                type="text"
                                                name="name"
                                                class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
                                                placeholder="Prénom et nom complet"
                                                required
                                                value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <div class="invalid-feedback" data-error-for="name">
                                                <?= isset($errors['name']) ? htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') : '' ?>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <input
                                                type="email"
                                                name="email"
                                                class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                                                placeholder="Adresse email"
                                                required
                                                value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <div class="invalid-feedback" data-error-for="email">
                                                <?= isset($errors['email']) ? htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') : '' ?>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <input
                                                type="tel"
                                                name="phone"
                                                class="form-control<?= isset($errors['phone']) ? ' is-invalid' : '' ?>"
                                                placeholder="Numéro de téléphone"
                                                required
                                                value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <div class="invalid-feedback" data-error-for="phone">
                                                <?= isset($errors['phone']) ? htmlspecialchars($errors['phone'], ENT_QUOTES, 'UTF-8') : '' ?>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <input
                                                type="date"
                                                name="date"
                                                class="form-control<?= isset($errors['date']) ? ' is-invalid' : '' ?>"
                                                required
                                                value="<?= htmlspecialchars($old['date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <div class="invalid-feedback" data-error-for="date">
                                                <?= isset($errors['date']) ? htmlspecialchars($errors['date'], ENT_QUOTES, 'UTF-8') : '' ?>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <input
                                                type="time"
                                                name="time"
                                                class="form-control<?= isset($errors['time']) ? ' is-invalid' : '' ?>"
                                                required
                                                value="<?= htmlspecialchars($old['time'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <div class="invalid-feedback" data-error-for="time">
                                                <?= isset($errors['time']) ? htmlspecialchars($errors['time'], ENT_QUOTES, 'UTF-8') : '' ?>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <select
                                                name="people"
                                                class="form-select<?= isset($errors['people']) ? ' is-invalid' : '' ?>"
                                                required>
                                                <option value="">Nombre de personnes</option>
                                                <option value="1" <?= (isset($old['people']) && $old['people'] === '1') ? 'selected' : '' ?>>1 personne</option>
                                                <option value="2" <?= (isset($old['people']) && $old['people'] === '2') ? 'selected' : '' ?>>2 personnes</option>
                                                <option value="3" <?= (isset($old['people']) && $old['people'] === '3') ? 'selected' : '' ?>>3 personnes</option>
                                                <option value="4" <?= (isset($old['people']) && $old['people'] === '4') ? 'selected' : '' ?>>4 personnes</option>
                                                <option value="5" <?= (isset($old['people']) && $old['people'] === '5') ? 'selected' : '' ?>>5 personnes</option>
                                                <option value="6" <?= (isset($old['people']) && $old['people'] === '6') ? 'selected' : '' ?>>6 personnes ou +</option>
                                            </select>
                                            <div class="invalid-feedback" data-error-for="people">
                                                <?= isset($errors['people']) ? htmlspecialchars($errors['people'], ENT_QUOTES, 'UTF-8') : '' ?>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <select
                                                name="occasion"
                                                class="form-select<?= isset($errors['occasion']) ? ' is-invalid' : '' ?>">
                                                <option value="">Occasion</option>
                                                <option value="birthday" <?= (isset($old['occasion']) && $old['occasion'] === 'birthday')    ? 'selected' : '' ?>>Anniversaire</option>
                                                <option value="anniversary" <?= (isset($old['occasion']) && $old['occasion'] === 'anniversary') ? 'selected' : '' ?>>Fête</option>
                                                <option value="business" <?= (isset($old['occasion']) && $old['occasion'] === 'business')    ? 'selected' : '' ?>>Dîner professionnel</option>
                                                <option value="date" <?= (isset($old['occasion']) && $old['occasion'] === 'date')        ? 'selected' : '' ?>>Soirée en couple</option>
                                                <option value="celebration" <?= (isset($old['occasion']) && $old['occasion'] === 'celebration') ? 'selected' : '' ?>>Célébration</option>
                                                <option value="other" <?= (isset($old['occasion']) && $old['occasion'] === 'other')       ? 'selected' : '' ?>>Autre</option>
                                            </select>
                                            <div class="invalid-feedback" data-error-for="occasion">
                                                <?= isset($errors['occasion']) ? htmlspecialchars($errors['occasion'], ENT_QUOTES, 'UTF-8') : '' ?>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <textarea
                                                class="form-control<?= isset($errors['message']) ? ' is-invalid' : '' ?>"
                                                name="message"
                                                rows="3"
                                                placeholder="Demandes particulières ou allergies"><?= htmlspecialchars($old['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                            <div class="invalid-feedback" data-error-for="message">
                                                <?= isset($errors['message']) ? htmlspecialchars($errors['message'], ENT_QUOTES, 'UTF-8') : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn-reserve w-100">
                                        <i class="bi bi-calendar-check me-2"></i>
                                        Réserver une table
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-7" data-aos="fade-left" data-aos-delay="300">
                            <div class="reservation-info-section">
                                <div class="hero-image">
                                    <img src="assets/img/restaurant/showcase-4.webp" alt="Salle du restaurant" class="img-fluid">
                                    <div class="overlay-content">
                                        <h4>Vivez une expérience gourmande</h4>
                                        <p>Savourez un moment chaleureux dans une ambiance authentique, où chaque plat est préparé avec passion.</p>
                                    </div>
                                </div>
                                <div class="info-cards">
                                    <div class="row g-3">
                                        <div class="col-md-6" data-aos="zoom-in" data-aos-delay="400">
                                            <div class="info-card">
                                                <div class="card-icon">
                                                    <i class="bi bi-clock"></i>
                                                </div>
                                                <div class="card-content">
                                                    <h5>Horaires</h5>
                                                    <p>Ouvert tous les jours<br>
                                                        11 h 00 - 23 h 00<br>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" data-aos="zoom-in" data-aos-delay="450">
                                            <div class="info-card">
                                                <div class="card-icon">
                                                    <i class="bi bi-geo-alt-fill"></i>
                                                </div>
                                                <div class="card-content">
                                                    <h5>Nous trouver</h5>
                                                    <p>7 Rue Cadet,<br>
                                                        75009 Paris</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" data-aos="zoom-in" data-aos-delay="500">
                                            <div class="info-card">
                                                <div class="card-icon">
                                                    <i class="bi bi-telephone-fill"></i>
                                                </div>
                                                <div class="card-content">
                                                    <h5>Réservations</h5>
                                                    <p>+33 1 45 23 90 03<br>
                                                        <small>Disponible tous les jours de 10h00 à 21h00</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" data-aos="zoom-in" data-aos-delay="550">
                                            <div class="info-card">
                                                <div class="card-icon">
                                                    <i class="bi bi-envelope-fill"></i>
                                                </div>
                                                <div class="card-content">
                                                    <h5>Nous écrire</h5>
                                                    <p>reservation@lepapacionu.com<br>
                                                        <small>Réponse sous 24 heures</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="additional-info" data-aos="fade-up" data-aos-delay="600">
                                    <div class="info-highlight">
                                        <i class="bi bi-star-fill"></i>
                                        <span>Il est recommandé de réserver 2 à 3 jours à l'avance pour les soirées du week-end</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="module" src="/assets/js/booking.js"></script>