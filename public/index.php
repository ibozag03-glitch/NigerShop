<?php
/**
 * Ce fichier est le point d'entrée de l'application NigerShop. Il initialise l'environnement, charge les dépendances et gère les requêtes entrantes.
*/
require_once dirname(__Dir__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
require_once dirname(__Dir__) . DIRECTORY_SEPARATOR . 'config/constants.php';

use App\Core\Router;

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$router = new Router(PUBLIC_REP);

$router->get('/', 'homePage', 'home')
       ->get('/annonces', 'annonces/index', 'annonces')
       ->get('/messages', 'messages/index', 'messages')
       ->get('/user', 'user/profile', 'user profile')
       ->get('/login', 'auth/login', 'connexion')
       ->get('/register', 'auth/register', 'inscription')
       ->run();