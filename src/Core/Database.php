<?php
/**
 * Ce fichier sert à se connecter à la base de donné ainsi qu'à traiter les requètes SQL
*/
namespace App\Core;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

class Database{

    private static ?PDO $database = null;    

    /**
     * Initialize database connection
     * 
     * @param string $DB_HOST Database host
     * @param string $DB_NAME Database name
     * @param string $DB_USER Database user
     * @param string $DB_PWD Database password
     * @throws PDOException If connection fails
     * @return void
     */
    public static function init(string $DB_HOST, string $DB_NAME, string $DB_USER, string $DB_PWD): void
    {
        $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=UTF8";
        try{
            self::$database = new PDO($dsn, $DB_USER, $DB_PWD,[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $pdoError){
            throw new PDOException("Erreur de connexion: " . $pdoError->getMessage());
        }
    }
    
    /**
     * Check if database is connected
     *
     * @return bool
     */
    public static function isConnected(): bool
    {
        return self::$database !== null;
    }

    /**
     * Execute a prepared query
     *
     * @param string $sql SQL query with placeholders
     * @param array $params Query parameters
     * @throws Exception If database not initialized or query fails
     * @return PDOStatement
     */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        if (self::$database === null) {
            throw new Exception("Database not initialized. Call Database::init() first.");
        }
        try {
            $stmt = self::$database->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (Exception $queryError) {
            throw new Exception("Query Error: " . $queryError->getMessage());
        }
    }

    /**
     * Fetch a single row
     *
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @throws Exception If database not initialized or query fails
     * @return ?array Single row as associative array, or null if not found
     */
    public static function fetch(string $sql, array $params = []): ?array
    {
        try{
            $stmt = self::query($sql, $params);
            $row = $stmt->fetch();
            return ($row === false) ? null : $row;
        } catch (Exception $fetchError){
            throw new Exception("Fetch Error: " . $fetchError->getMessage());
        }
    }

    /**
     * Fetch all rows
     *
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @throws Exception If database not initialized or query fails
     * @return array All rows as associative arrays
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        try {
            $result = self::query($sql, $params)->fetchAll();
            return $result;
        } catch (Exception $fetchAllError) {
            throw new Exception("FetchAll Error: " . $fetchAllError->getMessage());
        }
    }

    /**
     * Fetch a single column value
     *
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @throws Exception If database not initialized or query fails
     * @return mixed Column value, or false if no row
     */
    public static function fetchColumn(string $sql, array $params = []): mixed
    {
        try {
            $stmt = self::query($sql, $params);
            return $stmt->fetchColumn();
        } catch (Exception $fetchColumnError) {
            throw new Exception("FetchColumn Error: " . $fetchColumnError->getMessage());
        }
    }

    /**
     * Get last inserted row ID
     *
     * @throws Exception If database not initialized or operation fails
     * @return string|false Last insert ID or false
     */
    public static function lastInsertId(): string|false
    {
        try {
            return self::$database->lastInsertId();
        } catch (Exception $lastInsertIdError) {
            throw new Exception("LastInsertId Error: " . $lastInsertIdError->getMessage());
        }
    }
}
