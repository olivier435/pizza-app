# 🍕 Pizza App — Click & Collect en PHP (MVC Maison)

**Pizza App** est un projet pédagogique complet réalisé en PHP en architecture MVC maison (sans framework).
Il permet à un étudiant de comprendre la structuration d'une application web moderne :
- Routage  
- Contrôleurs GET/POST séparés
- Entités / Repositories
- Services (Mailer, ImageUploader, OrderNumber…)
- Sécurité
- Back-office complet façon EasyAdmin
- Upload d'images & génération WebP
- Emailing avec PHPMailer + Mailtrap
- Authentification, panier, commandes…


## 📦 Stack Technique
- **PHP 8+**
- **Composer**
- **MySQL / MariaDB**  
- **PHPMailer 7+** (Mailtrap pour le dev)  
- **Dotenv (vlucas/phpdotenv)**
- **Bootstrap 5**, AOS, Bootstrap Icons
- **Architecture MVC maison :**
    - `Controller`
    - `Entity`
    - `Repository`
    - `Service`
    - `templates/`
    - `public/`


## 🚀 Fonctionnalités

### 👤 Utilisateur
- Inscription / Connexion
- "Se souvenir de moi"
- Mot de passe oublié (token expirant + email sécurisé)
- Espace _Mon Compte_ :
    - Informations personnelles
    - Mot de passe
    - Suppression de compte
    - Historique des commandes
### 🛒 Panier & Commandes
- Panier dynamique (quantités, tailles…)
- Checkout
- Génération du numéro de commande :
`ORD-YYYY-000001`
- Email de confirmation
- Page de succès dédiée
- Page _Mes commandes_ avec images, détails accessoires
- Back-office : livraison / statut (`PAID` → `DELIVERED`)
### 🍕 Back-Office Admin
Accessible uniquement à role: `ADMIN` :
- Dashboard
- CRUD **Ingrédients**
    - switch boolean (vegan, végétarien, allergènes)
    - prix additionnel
    - protection anti-suppression si utilisé dans des pizzas
- CRUD **Pizzas**
    - gestion du slug
    - description auto-générée selon ingrédients
    - upload / suppression / conversion WebP (taille min 1024×683)
    - gestion des ingrédients associés
### 📩 Emails
- Réinitialisation du mot de passe
- Confirmation de commande
- Formulaire Contact
- Formulaire Réservation


## Arborescence du projet
```pgsql
pizza-app/
│
├── config/
│
├── MCD_MLD/
│
├── public/
│   ├── index.php
│   └── assets/
│       ├── css/
│       ├── img/restaurant/
│       ├── js/
│       └── vendor
│
├── src/
│   ├── Core/
│   ├── Controller/
│   │   ├── Admin/
│   │   └── Dev/
│   ├── Entity/
│   ├── Repository/
│   ├── Service/
│   └── Security/
│
├── templates/
│   ├── account/
│   ├── admin/
│   ├── auth/
│   ├── booking/
│   ├── cart/
│   ├── checkout/
│   ├── contact/
│   ├── contact/
│   ├── dev/
│   ├── email/
│   ├── home/
│   ├── layout/
│   ├── partials/
│   └── pizza/
│
├── .env
├── .env.local
├── .gitignore
├── composer.json
└── README.md
```


## ⚙️ Installation
### 1️⃣ Cloner le projet
``` bash
git clone https://github.com/olivier435/pizza-app.git
cd pizza-app
```
### 2️⃣ Installer les dépendances
``` bash
composer install
```
### 3️⃣ Configurer la base de données
Importer les scripts SQL fournis (tables user, pizza, ingredient, purchase, etc.) depuis le dossier `MCD_MLD`
### 4️⃣ Configurer l'environnement
Créer un fichier **.env.local** :
``` bash
APP_ENV=dev
APP_DEBUG=1
APP_SECRET=change-me-in-local
APP_URL=http://localhost:8000

DB_NAME=pizza
DB_HOST=127.0.0.1
DB_USER=root
DB_PASSWORD=null
DB_PORT=3306
DB_CHARSET=utf8mb4

FIXTURES_SECRET=PizzaSecret2025

MAIL_FROM=no-reply@pizza-app.com
MAIL_FROM_NAME=name_pizzeria
MAIL_CONTACT_TO=contact@pizzeria.com
MAIL_BOOKING_TO=reservation@pizzeria.com

SMTP_HOST=sandbox.smtp.mailtrap.io
SMTP_PORT=2525
SMTP_USER=xxxx
SMTP_PASS=xxxx
SMTP_SECURE=tls
```

**Récupérer vos identifiants Mailtrap**
- Connectez-vous à votre compte [Mailtrap](https://mailtrap.io/)
- Allez dans **Inboxes → Integration → PHPMailer**
- Copiez les paramètres d'intégration et collez-les dans `.env.local`
### Lancer le serveur
``` bash
php -S localhost:8000 -t public
```


## 🧪 Comptes de test
### 🧑‍💼 Administrateur
| Email                                     | Mot de passe |
| ----------------------------------------- | ------------ |
| [admin@gmail.com](mailto:admin@gmail.com) | password     |
### 👤 Utilisateur
| Email                                     | Mot de passe |
| ----------------------------------------- | ------------ |
| [user0@gmail.com](mailto:user0@gmail.com) | password     |
| [user1@gmail.com](mailto:user1@gmail.com) | password     |
### 📦 Création de Fixtures
Dans le terminal
```bash
php scripts/load-fixtures.php
```
Supprimer les fixtures
Dans le terminal
```bash
php scripts/clear-fixtures.php
```


## 🧠 Logique Métier (extraits pédagogiques)
### 🔸 Mot de passe oublié
1. Saisie email
2. Génération d'un token signé et daté (PasswordResetService)
3. Stockage + expiration (60 min)
4. Envoi mail avec lien sécurisé
5. Vérification du token
6. Modification du mot de passe
7. Invalidation de la demande
### 🔸 Workflow commande
1. Création d'un purchase en `PENDING`
2. Insertion des `purchase_item`
3. Calcul total
4. Génération numéro unique (`OrderNumberService`)
5. Passage en `PAID`
6. Email HTML
7. Page succès
8. Historique des commandes
9. Admin : statut → `DELIVERED`
### 🔸 Upload image WebP
- Vérification de format : png/jpg/webp
- Vérification dimensions min : 1024×683
- Redimensionnement si nécessaire
- Conversion WebP
- Nom basé sur le slug + timestamp
- Gestion suppression existante
- Service dédié : ImageUploader.php
### 🔐 Sécurité
- Sessions sécurisées
- Validation serveur (FormValidator)
- Rôle admin
- Tokens uniques
- Hash password PHP natif
- Anti-suppression FK (contrôle applicatif)
### 📌 Améliorations futures
- Recherche + pagination admin
- API JSON
- Module statistiques (top ventes)
- Dashboard avancé
- Documentation interne des services