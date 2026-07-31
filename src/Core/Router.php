<?php
/**
 * Ce fichier permet le routage vers les documents de l'application NigerShop. 
 * Il détermine quelle action doit être effectuée en fonction de la requête entrante 
 * et appelle le contrôleur approprié.
*/
namespace App\Core;

use \AltoRouter;
use Exception;

class Router {

    private string $basePath;
    public AltoRouter $router;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->router = new AltoRouter();
        // $this->router->setBasePath($basePath);
    }

    public function url(string $routeName, array $params = []): string
    {
        return $this->router->generate($routeName, $params);
    }

    public function get(string $url, string $path, ?string $name = null): Router
    {
        $this->router->map('GET', $url, $path, $name);
        return $this;
    }

    public function post(string $url, string $path, ?string $name = null): Router
    {
        $this->router->map('POST', $url, $path, $name);
        return $this;
    }

    public function put(string $url, string $path, ?string $name = null): Router
    {
        $this->router->map('PUT', $url, $path, $name);
        return $this;
    }

    public function delete(string $url, string $path, ?string $name = null): Router
    {
        $this->router->map('DELETE', $url, $path, $name);
        return $this;
    }

    public function run()
    {
        $match = $this->router->match();

        if (!$match) {
            throw new Exception("Aucune route ne correspond à l'Url demandée.", 404);
        }
        
        $target = $match['target'] ?? null;
        $params = $match['params'] ?? [];

        if (!$target) {
            throw new Exception("Cible de route non définie", 500);
        }

        $filePath = $this->basePath . $target . '.php';
        if (!file_exists($filePath)) {
            throw new Exception("Fiché de cible introuvable:" . $filePath, 404);
        }

        try {
            ob_start();
            require $filePath;
            $content = ob_get_clean();

            $layoutPath = require $this->basePath . 'views' . DIRECTORY_SEPARATOR . 'layouts.php';
            if (file_exists($layoutPath)) {
                ob_start();
                require $layoutPath;
                $content = ob_get_clean();
            }
    
            return $content;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'exécution de la route: " . $e->getMessage(), $e->getCode());
        }
    }
}