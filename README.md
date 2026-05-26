# NigerShop - Plateforme de Commerce Électronique

## Table des Matières

- [Description](#description-du-projet)
- [Fonctionnalités](#fonctionnalités-principales)
- [Technologies](#technologies-utilisées)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Guide d'utilisation](#guide-dutilisation)
- [Structure](#structure-du-projet)
- [Architecture](#architecture-mvc)
- [Roadmap](#plan-de-conception-roadmap)
- [Bonnes pratiques](#bonnes-pratiques-du-projet)
- [Contribution](#contribution)

## Description du Projet

NigerShop est une plateforme web de commerce électronique destinée aux petits et grands commerçants du Niger. 
Elle permet aux vendeurs de publier et gérer des annonces de produits (vêtements, nourriture, chaussures, électronique, etc.) 
et aux acheteurs de parcourir, rechercher, et communiquer avec les vendeurs.

Le projet est conçu comme une solution alternative à LeBonCoin, adaptée au marché nigérien.

## Fonctionnalités Principales

- **Authentification**: Inscription et connexion sécurisées avec hachage de mots de passe et vérification d'email
- **Gestion des annonces**: Créer, modifier, supprimer et rechercher des annonces avec images
- **Profil utilisateur**: Gestion du profil, avatar, historique des annonces publiés
- **Messagerie privée**: Communication entre vendeurs et acheteurs
- **Système de favoris**: Ajouter/supprimer des annonces favorites
- **Catégories**: Organisation des annonces par catégorie
- **Tableau de bord admin**: Gestion des utilisateurs et des statistiques de la plateforme

## Technologies Utilisées

### Frontend
- HTML5 (structure)
- CSS3 + Bootstrap 5 (mise en forme)
- JavaScript (interactivité)

### Backend
- PHP 8.5.6 (logique serveur)
- PDO (accès sécurisé à la base de données)
- MySQL/MariaDB (stockage des données)

### Environnement de Développement
- WAMPSERVER64 (serveur Apache + MySQL + PHP local)

## Prérequis

1. **WAMPSERVER64**
   - Apache (vert)
   - MySQL (vert)
   - Télécharger: [wampserver64](https://www.wampserver.com/)

2. **Git** pour versioner le projet

3. **Navigateur moderne** pour le visuel de la plateforme (Chrome, Firefox, Edge, etc)

## Installation

### 1. Cloner le projet

```bash
cd C:\wamp64\www
git clone https://github.com/ibozag03-glitch/NigerShop.git
cd NigerShop
```

### 2. Créer la base de données

Voir les instructions dans database/README.md

### 3. Configurer l'environnement local

1. Créez un fichier `.env` à la racine du projet
2. Copiez puis collez le contenu de `.env.example` dans `.env`
3. Adaptez les paramètres à votre environnement local

### 4. Lancer le projet

Accédez au site: http://localhost/NigerShop

## Guide d'utilisation

### Pour un Vendeur

1. S'inscrire
2. Se connecter
3. Créer une annonce
4. Gérer ses annonces
5. Répondre aux messages

### Pour un Acheteur

1. S'inscrire
2. Se connecter
3. Parcourir les annonces
4. Rechercher une annonce
5. Ajouter aux favoris
6. Contacter un vendeur

### Pour un Administrateur

1. Accéder au dashboard
2. Voir les statistiques de la plateforme
3. Gérer les utilisateurs

## Structure du Projet

### Architecture Générale

```
database/          Script de création de la base de donnée
config/            Configuration et constantes globales
src/               Toute la logique d'éxécution et de fonctionnement
assets/            CSS, JavaScript, images et fichiers uploadés
includes/          Composants réutilisables
pages/             Pages accessibles par utilisateurs
```

### Détail des Dossiers

```
NigerShop/
│
├── database/                      
│   ├── nigershop.sql               Schéma complet de la base de donnée
│   └── README.md                  Instructions de création de la BD
│
├── config/
│   ├── constants.php              Constantes globales (SITE_URL, UPLOAD_DIR, etc)
│   └── config.php                 Initialisation et chargement de la configuration
│
├── src/                           Toute la logique d'exécution et de fonctionnement du projet
│   │
│   ├── Core/                      Infrastructure de conception
│   │   ├── Database.php           Classe de connexion PDO (requêtes sécurisées)
│   │   ├── Router.php             Routeur URLs (pour futures APIs)
│   │   ├── Logger.php             Gestion des logs et débogage
│   │   └── Validator.php          Validation centralisée (emails, mots de passe)
│   │
│   ├── Models/                    Requêtes et opérations sur la base de données
│   │   ├── User.php               Gestion utilisateurs (CRUD Utilisateur)
│   │   ├── Annonce.php            Gestion annonces (CRUD Annonces)
│   │   ├── Message.php            Gestion messages privés (CRUD Messages)
│   │   ├── Category.php           Gestion catégories d'annonces (CRUD Catégorie)
│   │   └── Favorite.php           Gestion annonces favorites (CRUD Favoris)
│   │
│   ├── Controllers/               Logiques de vérification et de traitement des données
│   │   ├── AuthController.php     Logique authentification (login, register)
│   │   ├── AnnonceController.php  Logique annonces (création, modification)
│   │   ├── UserController.php     Logique profil utilisateur
│   │   ├── MessageController.php  Logique messagerie
│   │   ├── FavoriteController.php Logique favoris
│   │   └── AdminController.php    Logique tableau de bord admin
│   │
│   ├── Helpers/                   Fonctions utilitaires réutilisables
│   │   ├── FileUpload.php         Gestion uploads fichiers et images
│   │   ├── Session.php            Gestion sécurisée des sessions
│   │   └── Security.php           Protection CSRF et sécurité
│   │
│   └── Middleware/                Filtres de sécurité et contrôle d'accès
│       ├── AuthMiddleware.php     Vérifier que l'utilisateur est connecté
│       └── AdminMiddleware.php    Vérifier que l'utilisateur est administrateur
│
├── assets/                        Ressources de la plateforme (CSS, JavaScript, images, uploads)
│   │
│   ├── css/
│   │   ├── style.css              Styles CSS globaux du site
│   │   ├── responsive.css         Gestion du responsive du site (mobile, tablette, desktop)
│   │   └── variables.css          Variables CSS du site (couleurs, espacements)
│   │
│   ├── js/
│   │   ├── app.js                 JavaScript principal du site
│   │   ├── validation.js          Validation des formulaires côté client
│   │   └── helpers.js             Fonctions réutilisables JavaScript
│   │
│   ├── images/                    Logos, icônes et images
│   ├── videos/                    Vidéos publicitaires
│   │
│   └── uploads/                   Fichiers uploadés par les utilisateurs
│       ├── avatars/               Photos de profil
│       ├── annonces/              Photos des annonces
│       └── tmp/                   Fichiers temporaires
│
├── includes/                      Composants "raccourcis" réutilisables dans tout le site
│   ├── header.php                 Tête de page commune
│   ├── navbar.php                 Barre de navigation commune
│   ├── footer.php                 Pied de page commun
│   └── messages.php               Affichage messages d'erreur et succès
│
├── pages/                         Pages visibles par tous sur la plateforme
│   │
│   ├── auth/
│   │   ├── changeInfo.php         Formulaire pour gérer l'oubli des infos de compte par l'utilisateur
│   │   ├── login.php              Formulaire de connexion
│   │   └── register.php           Formulaire d'inscription
│   │
│   ├── annonces/
│   │   ├── index.php              Liste de toutes les annonces
│   │   ├── view.php               Détails d'une annonce
│   │   ├── create.php             Créer une nouvelle annonce
│   │   ├── edit.php               Modifier une annonce
│   │   ├── search.php             Rechercher et filtrer des annonces
│   │   └── categorie.php          Annonces organisé par catégorie
│   │
│   ├── messages/
│   │   ├── index.php              Pages d'accueil de la messagerie
│   │   ├── discussion.php         Discussion utilisateur & vendeur
│   │   ├── send.php               Envoyer un message
│   │   └── notification.php       Notifications des nouveaux messages
│   │
│   ├── user/
│   │   ├── profil.php             Infos personnel de l'utilisateur
│   │   ├── mes-annonces.php       Annonces propre à l'utilisateur
│   │   └── mes-favoris.php        Annonces favorites de l'utilisateur
│   │
│   ├── admin/
│   │   ├── dashboard.php          Statistiques et vue d'ensemble du site
│   │   └── user.php               Gestion des utilisateurs inscrit et présent sur le site
│   │
│   └── static/
│       ├── apropos.php            À propos de NigerShop
│       ├── cgvu.php               Conditions générales vente/utilisation
│       ├── confidentialite.php    Politique de confidentialité (RGPD)
│       └── faq.php                Questions fréquemment posées
│
├── index.php                      Page d'accueil du site
├── logout.php                     Déconnexion du site
├── .env                           Configuration locale
├── .env.example                   Template de configuration
├── .gitignore                     
└── README.md                      Documentation du projet
```

## Flux de Travail Général

### Architecture MVC

Le projet suit l'architecture MVC (Model-View-Controller):

1. **Model** (src/Models/): Accède et modifie la base de données
2. **Controller** (src/Controllers/): Traite la logique de fonctionnement
3. **View** (pages/): Affiche les données reçues du Controller

### Plan de conception (Roadmap):

1. Conception de la base de donnée `database/*` de la plateforme
2. Conception de .env, .env.exemple et de `config/*` pour le lancement de l'environnement de travail
3. Conception de `pages/auth/*` et logout pour le visuel des pages d'inscription et de connexion
4. Conception de `src/core/*` pour l'infrastructure de base de la plateforme
5. Conception de `src/models/*` pour gérer le C.R.U.D en Base de Donnée
6. Conception de `src/controllers/*` pour contrôler le traitement des données qui entre et circule dans la plateforme
7. Conception de register et login entièrement fonctionnel (CRUD utilisateur, contrôle des entrées, messages d'erreur et redirection /index)
8. Conception de `src/helpers/*` pour concevoir des fonctions d'aide pour les autres dossiers
9. Conception de `src/middleware/*` pour filtrer et contrôler les accès sur les différentes pages de la plateforme
10. Conception d'un visuel de la pages d'accueil de la plateforme
11. Conception de `pages/annonces/*` permettant de pouvoir créer, modifier et chercher des annonces sur la plateforme
12. Conception de `pages/messages/*` permettant au utilisateur de marchander sur différentes annonces de la plateforme
13. Conception de `pages/user/*` permettant à l'utilisateur d'avoir un visuel sur ses infos personnel et ses avtivité sur la plateforme
14. Conception de `pages/admin/*` permettant à l'administrateur de réguler les activité sur la plateforme
15. Conception finale de la pages d'accueil de la plateforme

### Exemple de logique d'exécution: Créer une Annonce

1. Utilisateur accède à pages/annonces/create.php
2. AuthMiddleware vérifie qu'il est connecté
3. Formulaire est rempli et soumis
4. create.php appelle AnnonceController
5. Controller valide les données
6. Controller appelle AnnonceModel pour insérer en Base de Donnée
7. Model exécute la requête SQL sécurisée
8. Résultat retourne au Controller
9. Controller retourne le résultat à la Vue
10. Vue affiche un message de succès ou d'erreur

Et BIM !! Vous venez de créer une Annonce !!

### Sécurité

- **PDO Prepared Statements**: Protection contre injection SQL
- **Mot de pass Hashé**: Hachage sécurisé des mots de passe
- **Gestion des sessions**: Gestion sécurisée des sessions utilisateur
- **Validation des entrées**: Validation côté serveur et client

## Bonnes Pratiques du Projet

1. **Séparation des responsabilités**: Chaque fichier a une seule responsabilité ( *diviser pour mieux reigner ;)* )
2. **Réutilisabilité**: Code partagé dans Models, Controllers et Helpers
3. **Maintenabilité**: Structure claire et organisation logique
4. **Sécurité**: PDO, hachage, validation, surveillance d'accéssiblité
5. **Extensibilité**: Facile d'ajouter de nouvelles fonctionnalitées

## Contribution

Pour contribuer au projet:

1. Fork le repository
2. Créez une branche (`git checkout -b feature/Nom_de_Feature`)
3. Committez vos changements (`git commit -m 'Ajout: description'`)
4. Pushez (`git push origin feature/Nom_de_Feature`)
5. Ouvrez une Pull Request

## Licence

Ce projet est sous Licence Apache-2.0

## Support

Pour toute question ou problème:
- Créez une issue sur GitHub
- Consultez la FAQ (pages/static/faq.php) 

---
**Version**: 1.0.0 (Version de départ);

**Dernière mise à jour**: Mai 2026 (En cours de développement);

**Développeur**: Ismaël GAZOBI (Étudiant Bachelor Informatique);