<?php
/**
 * Ce fichier contient des chemins et des valeurs qui seront réutilisé dans le site comme des variables globales constantes.
 * On y trouve:
 * - les infos sur le site; 
 * - les chemins d'accès aux différents répertoires du projet;
 * - les chemins d'accès pour les fichiers uploadé;
 * - les permissions d'accèes utilisateur & admin;
 * - des valeurs de validation d'accès (register & login);
 * - des messages de validation d'accès (register & login);
 *
 * NB: La quantité de valeur/variable sera mise à jours au fur et à mesure de l'évolution de la plateforme.
*/

// URLs de la plateforme
define('SITE_URL','http://localhost/NigerShop');
define('SITE_NAME','NigerShop');
define('SITE_DESCRIPTION','plateforme web de commerce électronique destinée aux commerçants du Niger.');

// Chemins vers les repertoires
define('NS_REP', __DIR__ . DIRECTORY_SEPARATOR .'..' . DIRECTORY_SEPARATOR);
define('SRC_REP', NS_REP . 'src' . DIRECTORY_SEPARATOR);
define('ASSETS_REP', NS_REP . 'assets' . DIRECTORY_SEPARATOR);
define('INCLUDES_REP', NS_REP . 'includes' . DIRECTORY_SEPARATOR);
define('PUBLIC_REP', NS_REP. 'public' . DIRECTORY_SEPARATOR);

// Chemin pour les uploads
define('UPLOAD_REP', ASSETS_REP . 'uploads/');
define('UPLOAD_ANNONCE_REP', UPLOAD_REP . 'annonces/');
define('UPLOAD_AVATAR_REP', UPLOAD_REP . 'avatars/');

// Permission d'accès
define('PERMISSION_USER',0);
define('PERMISSION_ADMIN',1);

// Valeur de validation
define('MIN_PWD_LEN', 8);
define('MAX_PWD_LEN', 255);
define('MIN_USERNAME_LEN', 3);
define('MAX_USERNAME_LEN', 50);
define('MIN_ANNONCE_TITRE_LEN', 5);
define('MAX_ANNONCE_TITRE_LEN', 200);
define('MIN_ANNONCE_DESC_LEN', 20);
define('MAX_ANNONCE_DESC_LEN', 5000);

// Messages de validation
define('MSG_REGISTER_SUCCESS','Votre compte à été créé avec succès !');
define('MSG_LOGIN_SUCCESS','Connexion réussi avec succès !');
define('MSG_INVALID_EMAIL','Cette Email n\'est pas valides.');
define('MSG_ERREUR_EMAIL_EXISTS','Cette Email existe déjà.');
define('MSG_INVALID_PWD','Ce mot de passe n\'est pas valide.');
define('MSG_ERREUR_PWD_COURT','Le mot de passe est trop court.');