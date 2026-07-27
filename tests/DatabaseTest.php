<?php
require_once dirname(__Dir__) . DIRECTORY_SEPARATOR . 'config/config.php';

use PHPUnit\Framework\TestCase;
use App\Core\Database;
use PDOStatement;

final class DatabaseTest extends TestCase
{
    /**
     * Set up before all tests - initialize database once
     */
    public static function setUpBeforeClass(): void
    {
        chargerEnv(NS_REP.'.env');

        $DB_HOST = $_ENV["DB_HOST"];
        $DB_NAME = $_ENV["DB_NAME"];
        $DB_USER = $_ENV["DB_USER"];
        $DB_PASS = $_ENV["DB_PASS"];
        
        Database::init($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS);
    }

    /**
     * Set up fixtures before each test
     */
    protected function setUp(): void
    {
       Database::query("DELETE FROM users");

       $sql = "INSERT INTO users (firstname, lastname, email, password, address, phone, birth_date, role)
            VALUES (:firstname, :lastname, :email, :password, :address, :phone, :birth_date, 'admin');";
       $params = [
            "firstname" => "leamsi",
            "lastname" => "ibozag",
            "email" => "leamsiibozag@gmail.com",
            "password" => "AzertyQwerty123!",
            "address" => "Somewhere here at my location In The Wolrd",
            "phone" => "+277 86 61 20 16",
            "birth_date" => "07/02/2007"
        ];

       Database::query($sql, $params);
    }

    /**
     * Test that database is connected after initialization
     */
    public function testIsConnected()
    {
        $result = Database::isConnected();
        self::assertTrue($result);
    }

    /**
     * Test query returns PDOStatement
     */
    public function testQuery()
    {
        $sql = "SELECT * FROM users WHERE user_id = :id";
        $params = ["id" => 1];

        $query = Database::query($sql, $params);
 
        self::assertInstanceOf(PDOStatement::class, $query);
    }

    /**
     * Test fetch returns array
     */
    public function testFetchReturnsArray()
    {
        $sql = "SELECT * FROM users WHERE user_id = :id";
        $params = ["id" => 1];

        $fetchReturnsArray = Database::fetch($sql, $params);

        self::assertIsArray($fetchReturnsArray);
        self::assertNotEmpty($fetchReturnsArray);
        self::assertEquals($fetchReturnsArray['user_id'], 1);
    }

    /**
     * Test fetch returns null
     */
    public function testFetchReturnsNull()
    {
        $sql = "SELECT * FROM users WHERE user_id = :id";
        $params = ["id" => 5];

        $fetchReturnsNull = Database::fetch($sql, $params);

        self::assertNull($fetchReturnsNull);
    }

    /**
     * Test fetchAll returns array
     */
    public function testFetchAll()
    {
        $sql = "SELECT * FROM users WHERE user_id = :id";
        $params = ["id" => 1];

        $fetchAll = Database::fetchAll($sql, $params);

        self::assertIsArray($fetchAll);
        self::assertGreaterThanOrEqual(1, count($fetchAll));
    }

    /**
     * Test fetchAll returns array
     */
    public function testFetchColumn()
    {
        $sql = "SELECT COUNT(*) FROM users";

        $fetchColumn = Database::fetchColumn($sql);
        
        self::assertIsInt($fetchColumn);
        self::assertGreaterThan(0, $fetchColumn);
    }

    /**
     * Test lastInsertId returns string or false
     */
    public function testLastInsertId()
    {
        $sql = "INSERT INTO users (firstname, lastname, email, password, address, phone, birth_date, role)
                VALUES (:firstname, :lastname, :email, :password, :address, :phone, :birth_date, 'admin');";
        $params = [
            "firstname" => "ibozag",
            "lastname" => "leamsi",
            "email" => "ibozag_leamsi@gmail.com",
            "password" => "QwertyAzerty123!",
            "address" => "Somewhere here at my location In The Wolrd",
            "phone" => "+277 86 16 61 20",
            "birth_date" => "07/03/2007",
        ];

        Database::query($sql, $params);
        
        $result = Database::lastInsertId();

        self::assertTrue(is_string($result) || $result === false);
        self::assertNotEmpty($result);
    }

    /**
     * Test query throws exception when database not initialized
     */
    public function testQueryThrowsExceptionWithInvalidSQL(): void
    {
        $this->expectException(Exception::class);
        Database::query("QUERY");
    }
}