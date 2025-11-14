<?php
/** @var string $resetUrl */
/** @var string $email */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de votre mot de passe</title>
</head>
<body>
    <h1>Réinitialisation de votre mot de passe</h1>

    <p>Bonjour,</p>
    <p>
        Vous avez demandé à réinitialiser le mot de passe associé à l'adresse
        <strong><?= htmlspecialchars($email) ?></strong>.
    </p>
    <p>
        Pour choisir un nouveau mot de passe, cliquez sur le lien ci-dessous :
    </p>
    <p>
        <a href="<?= htmlspecialchars($resetUrl) ?>">Réinitialiser mon mot de passe</a>
    </p>
    <p>
        Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet e-mail.
    </p>
    <p>À bientôt,<br>L'équipe Le Papacionu</p>
</body>
</html>