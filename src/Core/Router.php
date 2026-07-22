<?php
/**
 * Ce fichier permet le routage vers les documents de l'application NigerShop. 
 * Il détermine quelle action doit être effectuée en fonction de la requête entrante 
 * et appelle le contrôleur approprié.
*/
namespace App\Core;

use \AltoRouter;

class Router {

    private string $basePath;
    private AltoRouter $router;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->router = new AltoRouter();
    }

    public function url(string $routeName, array $params = []): string
    {
        return $this->router->generate($routeName, $params);
    }

    public function getRoute(string $url, string $path, ?string $name = null)
    {
        $this->router->map('GET', $url, $path, $name);
        return $this;
    }

    public function run()
    {
        $match = $this->router->match();
        $path = $match['target'] ?? null;
        $params = $match['params'] ?? [];

        ob_start();
        require $this->basePath . DIRECTORY_SEPARATOR . $path . '.php';
        $content = ob_get_clean();
        require $this->basePath . DIRECTORY_SEPARATOR . 'views/layouts.php';

        return $this;
    }
}