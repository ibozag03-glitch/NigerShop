<?php
/**
 * Ce fichier permet la configuration de l'environnement de travail.
 * Il recupère les constantes et remplie les variables d'environnement
 * grâce au fichier env.
*/

//Recuperation des constantes
require_once __DIR__.'/constants.php';

//Configuration des variables de l'environnement depuis env
function chargerEnv(string $fichierEnv){
    if(!file_exists($fichierEnv)){
        die('Fichier d\'environnement ".env" introuvable.');
    }
    $lines = file($fichierEnv);
    foreach($lines as $line){
        if(strpos($line, "#") === 0 || strpos($line, '=') === false){
            continue;
        }
        list($variables,$valeurs) = explode('=',$line);
        $variable = trim($variables);
        $valeur = trim($valeurs);

        $_ENV[$variable] = $valeur;
        putenv("$variable=$valeur");
    }
}

chargerEnv(NS_REP.'.env');

$DB_HOST = $_ENV["DB_HOST"];
$DB_NAME = $_ENV["DB_NAME"];
$DB_USER = $_ENV["DB_USER"];
$DB_PWD = '';//replace this with $_ENV["DB_PWD"];

// AUTOLOADER PSR-4 - Charge automatiquement les classes du dossier src/
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    
    $base_dir = __DIR__ . '/../src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});