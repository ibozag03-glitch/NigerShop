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