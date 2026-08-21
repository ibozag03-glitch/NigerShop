<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config/config.php';

use App\Core\Database;
use App\Models\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase {

    private static User $user;
    private static array $User = [
        "firstname" => "User",
        "lastname" => "Testing",
        "email" => "testingUser@gmail.com",
        "password" => "iAmTestingUserNO1!",
        "address" => "here in the code",
        "phone" => "+227 44 61 20 16",
        "birth_date" => "07/07/303O",
        "role" => "user",
        "status" => "active"
    ];

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

    public function tearDown(): void
    {
        if (Database::isConnected()) {
            Database::query("DELETE FROM users WHERE email LIKE :email", ["email" => "%@gmail.com"]);
        }
    }

    public function setTestUser(): User
    {
        $user = new User();
        $user->setFirstname(self::$User["firstname"])
            ->setLastname(self::$User["lastname"])
            ->setEmail(self::$User["email"])
            ->setPassword(self::$User["password"])
            ->setAddress(self::$User["address"])
            ->setPhone(self::$User["phone"])
            ->setBirthDate(self::$User["birth_date"])
            ->setRole()
            ->setStatus();
        return $user;
    }

    public function testFindUserByIdReturnsNull()
    {
        try {
            if (Database::isConnected()) {
                $NotFoundedUser = User::findById(9999);            
                self::assertNull($NotFoundedUser);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testFindUserByIdReturnsUser()
    {
        try {
            if (Database::isConnected()) {
                $user = $this->setTestUser();
                $user->create();
                $userId = $user->getUserId();
                $foundedUser = User::findById($userId);
                self::assertInstanceOf(User::class, $foundedUser);
                self::assertSame($userId, $foundedUser->getUserId());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testFindUserByEmailReturnsNull()
    {
        try {
            if (Database::isConnected()) {
                $user = $this->setTestUser();
                $user->create();
                $email = "ibobo@gmail.com";
                $NotFoundedUser = User::findByEmail("$email");
                self::assertNull($NotFoundedUser);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testFindUserByEmailReturnsUser()
    {
        try {
            if (Database::isConnected()) {
                $user = $this->setTestUser();
                $user->create();
                $email = $user->getEmail();
                $foundedUser = User::findByEmail("$email");
                self::assertInstanceOf(User::class, $foundedUser);
                self::assertSame(self::$User["email"], $email);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testFindAllUsers()
    {
        try {
            if (Database::isConnected()) {
                $user = $this->setTestUser();
                $user->create();
                $Users = User::findAll();
                self::assertIsArray($Users);
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
                //set the user, create it and check the creation
                $user = $this->setTestUser();
                $validUser = $user->create();
                self::assertTrue($validUser);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testCreateInvalidUser()
    {
        try {
            if (Database::isConnected()) {
                //set user
                $user = $this->setTestUser();

                //put invalid data
                $invalidUserData = $user->setFirstname("b")
                    ->setEmail("wmgmail.cm")
                    ->setPassword("wrongPass");

                //check the exceptions
                self::expectException(Exception::class);
                self::expectExceptionMessage("VALIDATION ERROR : firstname est trop court.; Cette Email n'est pas valides.; Ce mot de passe n'est pas valide.");
                $invalidUserData->create();
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testUpdateUserWithValidData()
    {
        try {
            if (Database::isConnected()) {
                //set the user
                $user = $this->setTestUser();
                $user->create();

                //update data
                $user->setFirstname("testing")
                    ->setLastname("update")
                    ->setEmail("updatedEmail@gmail.com")
                    ->setAddress("updated Address");
                $updatedUser = $user->update();
                
                //check the updates
                self::assertInstanceOf(User::class, $user);
                self::assertTrue($updatedUser);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }
    
    public function testInvalidUserUpdate()
    {
        try {
            if (Database::isConnected()) {
                //set user
                $user = $this->setTestUser();
                $user->create();
                
                //invalid data
                $invalidUserData = $user->setFirstname("b")
                    ->setEmail("wm@gmail.com")
                    ->setPassword("wrongPass");

                // check exceptions
                self::expectException(Exception::class);
                self::expectExceptionMessageMatches("VALIDATION ERROR : firstname est trop court.; Cette Email n'est pas valides.; Ce mot de passe n'est pas valide.");
                $invalidUserData->update();
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    public function testDeleteUser()
    {
        try {
            if (Database::isConnected()) {
                $user = $this->setTestUser();
                $user->create();
                self::assertTrue($user->delete());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

}