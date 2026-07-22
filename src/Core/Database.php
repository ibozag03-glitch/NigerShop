<?php
/**
 * Ce fichier sert à se connecter à la base de donné ainsi qu'à traiter les requètes SQL
*/
namespace App\Core;
use PDO;
use PDOException;
use PDOStatement;

class Database{

    private static PDO $dataBase;    

    public function __construct(string $DB_HOST,string $DB_NAME,string $DB_USER,string $DB_PWD)
    {// connexion à la base de donnée
        $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=UTF8";
        try{
            return self::$dataBase = new PDO($dsn, $DB_USER, $DB_PWD,[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }catch(PDOException $e){
            throw new PDOException("Erreur de connexion: " . $e->getMessage());
        }
    }
    
    public static function query(string $sql, array $params = []): PDOStatement
    {// permet d'exécuter les requètes preparées
        $stmt = self::$dataBase->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {// permet de récupérer un résultat spécifique d'une requète préparée
        $stmt = self::query($sql, $params);
        $row = $stmt->fetch();
        return ($row === false) ? null : $row;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {// permet de récupérer tout les résultats d'une requète préparée
        $stmt = self::query($sql, $params)->fetchAll();
        return $stmt;
    }

    public static function fetchColumn(string $sql, array $params = []): mixed
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchColumn();
    }

    public static function lastInsertId(): string|false
    {// permet de récupérer le dernier id enregister ou modifié
        return self::$dataBase->lastInsertId();
    }
}
