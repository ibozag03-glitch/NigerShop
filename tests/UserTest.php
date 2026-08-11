<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config/config.php';

use App\Core\Database;
use App\Models\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase {

    private static User $user;
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

        self::$user = new User();
    }

    public function testFindUserByIdReturnsNull()
    {
        try {
            if (Database::isConnected()) {
                $user = User::findById(19);            
                self::assertNull($user);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testFindUserByIdReturnsUser()
    {
        try {
            if (Database::isConnected()) {
                $user = User::findById(67);
                dump($user);
                self::assertInstanceOf(User::class, $user);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testFindUserByEmailReturnsNull()
    {
        try {
            if (Database::isConnected()) {
                $user = User::findByEmail("gogogaga@gmail.com");
                self::assertNull($user);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testFindUserByEmailReturnsUser()
    {
        try {
            if (Database::isConnected()) {
                $user = User::findByEmail("ibozagleamsi@gmail.com");
                self::assertInstanceOf(User::class, $user);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testFindAllUsers()
    {
        try {
            if (Database::isConnected()) {
                $user = User::findAll();
                self::assertIsArray($user);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testFindNoUsers()
    {
        try {
            if (Database::isConnected()) {
                Database::query("DELETE FROM users");
                $user = User::findAll();
                self::assertEmpty($user);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testCreateValidUser()
    {
        try {
            if (Database::isConnected()) {
                $user = self::$user->setFirstname("ibozag")
                        ->setLastname("leamsi")
                        ->setEmail("ibozagleamsi@gmail.com")
                        ->setPassword("QwertyAzerty123!")
                        ->setAddress("some where")
                        ->setPhone("+227 38 48 59 69")
                        ->setBirthDate("07/07/3007")
                        ->create();
                self::assertTrue($user);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testUpdateUser()
    {
        try {
            if (Database::isConnected()) {
                $user = self::$user->setUserId(67)
                        ->setFirstname("leamsiiii")
                        ->setLastname("ibozag")
                        ->setEmail("ibobogmail.com")
                        ->setPassword("qwertyAzerty12!")
                        ->setAddress("some where")
                        ->setPhone("+227 38 48 59 69")
                        ->setBirthDate("07/07/3007")
                        ->update();
                self::assertTrue($user);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testDeleteUser()
    {
        try {
            if (Database::isConnected()) {
                $user = self::$user->setUserId(66)
                        ->delete();
                self::assertTrue($user);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

}