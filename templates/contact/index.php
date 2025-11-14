<?php
$old    = $old    ?? [];
$errors = $errors ?? [];
?>
<section class="inner-hero section dark-background"></section>
<section id="contact" class="contact section light-background">
    <?php include __DIR__ . '/../layout/_flash.php'; ?>
    <div class="container section-title" data-aos="fade-up">
        <span class="description-title">Contact</span>
        <h2>Contact</h2>
        <p>Une demande particulière ou simplement envie de nous parler ? Notre équipe se fera un plaisir de vous répondre.</p>
    </div>
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4 mb-5">
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="contact-info-box">
                    <div class="icon-box">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div class="info-content">
                        <h4>Notre adresse</h4>
                        <p>7 Rue Cadet, 75009 Paris</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="contact-info-box">
                    <div class="icon-box">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h4>Adresses email</h4>
                        <p>info@example.com</p>
                        <p>contact@example.com</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="contact-info-box">
                    <div class="icon-box">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div class="info-content">
                        <h4>Horaires d'ouverture</h4>
                        <p>Ouvert tous les jours</p>
                        <p>11 h 00 - 23 h 00</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="map-section" data-aos="fade-up" data-aos-delay="200">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5248.255411229744!2d2.340251476756745!3d48.87484199954822!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e3f19ca9dab%3A0xf0d81dfcfd1eb23b!2sLe%20Papacionu%20Paris!5e0!3m2!1sfr!2sfr!4v1763042974666!5m2!1sfr!2sfr" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <div class="container form-container-overlap">
        <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="300">
            <div class="col-lg-10">
                <div class="contact-form-wrapper">
                    <h2 class="text-center mb-4">Nous contacter</h2>
                    <form id="contact-form" action="/contact" method="post" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-with-icon">
                                        <i class="bi bi-person"></i>
                                        <input
                                            type="text"
                                            class="form-control<?= isset($errors['firstname']) ? ' is-invalid' : '' ?>"
                                            name="firstname"
                                            placeholder="Prénom"
                                            value="<?= htmlspecialchars($old['firstname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            required
                                        >
                                        <div class="invalid-feedback" data-error-for="firstname">
                                            <?= isset($errors['firstname']) ? htmlspecialchars($errors['firstname'], ENT_QUOTES, 'UTF-8') : '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-with-icon">
                                        <i class="bi bi-person"></i>
                                        <input
                                            type="text"
                                            class="form-control<?= isset($errors['lastname']) ? ' is-invalid' : '' ?>"
                                            name="lastname"
                                            placeholder="Nom"
                                            value="<?= htmlspecialchars($old['lastname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            required
                                        >
                                        <div class="invalid-feedback" data-error-for="lastname">
                                            <?= isset($errors['lastname']) ? htmlspecialchars($errors['lastname'], ENT_QUOTES, 'UTF-8') : '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-with-icon">
                                        <i class="bi bi-text-left"></i>
                                        <input
                                            type="email"
                                            class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                                            name="email"
                                            placeholder="Adresse email"
                                            value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            required
                                        >
                                        <div class="invalid-feedback" data-error-for="email">
                                            <?= isset($errors['email']) ? htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') : '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-with-icon">
                                        <i class="bi bi-text-left"></i>
                                        <input
                                            type="text"
                                            class="form-control<?= isset($errors['subject']) ? ' is-invalid' : '' ?>"
                                            name="subject"
                                            placeholder="Sujet"
                                            value="<?= htmlspecialchars($old['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            required
                                        >
                                        <div class="invalid-feedback" data-error-for="subject">
                                            <?= isset($errors['subject']) ? htmlspecialchars($errors['subject'], ENT_QUOTES, 'UTF-8') : '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="input-with-icon">
                                        <i class="bi bi-chat-dots message-icon"></i>
                                        <textarea
                                            class="form-control<?= isset($errors['message']) ? ' is-invalid' : '' ?>"
                                            name="message"
                                            placeholder="Écrire le message..."
                                            style="height: 180px"
                                            required
                                        ><?= htmlspecialchars($old['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                        <div class="invalid-feedback" data-error-for="message">
                                            <?= isset($errors['message']) ? htmlspecialchars($errors['message'], ENT_QUOTES, 'UTF-8') : '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-submit">Envoyer le message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="module" src="/assets/js/contact.js"></script>