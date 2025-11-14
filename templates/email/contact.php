<?php

/** @var array $data */
$firstname = $data['firstname'] ?? '';
$lastname  = $data['lastname']  ?? '';
$email     = $data['email']     ?? '';
$subject   = $data['subject']   ?? '';
$message   = $data['message']   ?? '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Nouveau message de contact</title>
</head>

<body>
    <h1>Nouveau message de contact</h1>
    <p>
        Vous avez reçu un nouveau message depuis le formulaire de contact de votre site.
    </p>
    <h2>Informations de l'expéditeur</h2>
    <ul>
        <li><strong>Prénom :</strong> <?= htmlspecialchars($firstname) ?></li>
        <li><strong>Nom :</strong> <?= htmlspecialchars($lastname) ?></li>
        <li><strong>Email :</strong> <?= htmlspecialchars($email) ?></li>
    </ul>
    <h2>Sujet</h2>
    <p><?= nl2br(htmlspecialchars($subject)) ?></p>
    <h2>Message</h2>
    <p><?= nl2br(htmlspecialchars($message)) ?></p>
</body>

</html>