<?php
/**
 * Ce fichier est le point d'entrée de l'application NigerShop. Il initialise l'environnement, charge les dépendances et gère les requêtes entrantes.
*/
require_once '../vendor/autoload.php';
require_once '../config/constants.php';

use App\Core\Router;

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$router = new Router(PUBLIC_REP);

$router->getRoute('/', 'homePage', 'home')
       ->getRoute('/annonces', 'annonces/index', 'Annonces')
       ->getRoute('/messages', 'messages/index', 'Messages')
       ->getRoute('/user', 'user/profil', 'Profil Utilisateur')
       ->getRoute('/login', 'auth/login', 'Connexion')
       ->getRoute('/register', 'auth/register', 'Inscription')
       ->run();