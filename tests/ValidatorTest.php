<?php
require_once dirname(__Dir__) . DIRECTORY_SEPARATOR . 'config/config.php';

use PHPUnit\Framework\TestCase;
use App\Core\Database;
use App\Core\Validator;

final class ValidatorTest extends TestCase
{
    private static bool $dbReady = false;
    private static bool $hasUsersTable = false;
    private static bool $hasCategoriesTable = false;
    private static ?int $categoryId = null;
    private static string $duplicateEmail = 'nigershop_test_duplicate@example.com';
    private static string $uniqueEmail = 'nigershop_test_unique@example.com';
    private static string $categorySlug = 'nigershop-test-cat';
    private static string $categoryName = 'NigerShop Test Category';

    /**
     * Initialize the shared test database connection and fixtures.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        chargerEnv(NS_REP . '.env');
        Database::init(
            $_ENV["DB_HOST"],
            $_ENV["DB_NAME"],
            $_ENV["DB_USER"],
            $_ENV["DB_PASS"]
        );

        self::$dbReady = true;
        self::$hasUsersTable = self::tableExists('users');
        self::$hasCategoriesTable = self::tableExists('categories');

        if (self::$hasCategoriesTable) {
            self::$categoryId = self::createTestCategory();
        }

        if (self::$hasUsersTable) {
            self::ensureTestUser(self::$duplicateEmail);
            self::deleteTestUser(self::$uniqueEmail);
        }
    }

    /**
     * Remove test fixtures created for the suite.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        if (self::$dbReady && self::$hasUsersTable) {
            self::deleteTestUser(self::$duplicateEmail);
            self::deleteTestUser(self::$uniqueEmail);
        }

        if (self::$dbReady && self::$hasCategoriesTable) {
            self::deleteTestCategory();
        }
    }

    /**
     * Check whether the given table exists in the connected test database.
     *
     * @param string $name Table name
     * @return bool
     */
    private static function tableExists(string $name): bool
    {
        if (!self::$dbReady) {
            return false;
        }

        try {
            $result = Database::fetchColumn("SHOW TABLES LIKE :table", ['table' => $name]);
            return $result !== false && (int) $result > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create or return a deterministic category fixture for tests.
     *
     * @return int
     */
    private static function createTestCategory(): int
    {
        $existing = Database::fetch("SELECT category_id FROM categories WHERE slug = :slug OR name = :name", [
            'slug' => self::$categorySlug,
            'name' => self::$categoryName,
        ]);

        if ($existing !== null && isset($existing['category_id'])) {
            return (int) $existing['category_id'];
        }

        Database::query(
            "INSERT INTO categories (slug, name, descriptions) VALUES (:slug, :name, :descriptions)",
            [
                'slug' => self::$categorySlug,
                'name' => self::$categoryName,
                'descriptions' => 'Test category for ValidatorTest',
            ]
        );

        return (int) Database::lastInsertId();
    }

    /**
     * Remove the deterministic category fixture.
     *
     * @return void
     */
    private static function deleteTestCategory(): void
    {
        Database::query("DELETE FROM categories WHERE slug = :slug", ['slug' => self::$categorySlug]);
    }

    /**
     * Ensure the given test user exists for duplicate-value verification.
     *
     * @param string $email
     * @return int
     */
    private static function ensureTestUser(string $email): int
    {
        $existing = Database::fetch("SELECT user_id FROM users WHERE email = :email", ['email' => $email]);
        if ($existing !== null && isset($existing['user_id'])) {
            return (int) $existing['user_id'];
        }

        Database::query(
            "INSERT INTO users (firstname, lastname, email, password, address, phone, birth_date, role) VALUES (:firstname, :lastname, :email, :password, :address, :phone, :birth_date, :role)",
            [
                'firstname' => 'Test',
                'lastname' => 'User',
                'email' => $email,
                'password' => "Test123!@#",
                'address' => '123 Test Ave',
                'phone' => '+22712345678',
                'birth_date' => '1990-01-01',
                'role' => 'user',
            ]
        );

        return (int) Database::lastInsertId();
    }

    /**
     * Delete the deterministic test user by email.
     *
     * @param string $email
     * @return void
     */
    private static function deleteTestUser(string $email): void
    {
        Database::query("DELETE FROM users WHERE email = :email", ['email' => $email]);
    }

    /**
     * Instantiate a fresh Validator for each test.
     *
     * @return Validator
     */
    public function getValidator(): Validator
    {
        return new Validator();
    }

    /**
     * Verify that required() accepts a non-empty value.
     *
     * @return void
     */
    public function testRequiredField()
    {
        $field = "email";
        $value = "test_email@gmail.c";
        
        $required = self::getValidator()->required($field, $value);
        $valid = $required->isValid();
        
        self::assertInstanceOf(Validator::class, $required);
        self::assertTrue($valid);
    }

    /**
     * Verify that validName() accepts a valid username.
     *
     * @return void
     */
    public function testValidNameInput()
    {
        $field = "name"; $value = "ibozag";
        
        $validName = self::getValidator()->validName($field,$value);
        $valid = $validName->isValid();
        
        self::assertInstanceOf(Validator::class, $validName);
        self::assertTrue($valid);
    }

    /**
     * Verify that validEmail() accepts a properly formatted email.
     *
     * @return void
     */
    public function testValidEmailInput()
    {
        $field = "email"; $value = "testemail@gmail.com";
        
        $validEmail = self::getValidator()->validEmail($field,$value);
        $valid = $validEmail->isValid();
        
        self::assertInstanceOf(Validator::class, $validEmail);
        self::assertTrue($valid);
    }

    /**
     * Verify that validPassword() accepts a strong password.
     *
     * @return void
     */
    public function testValidPasswordInput()
    {
        $field = "password"; $value = "AzertyQwerty12!!!";
        
        $validPassword = self::getValidator()->validPassword($field,$value);
        $valid = $validPassword->isValid();
        
        self::assertInstanceOf(Validator::class, $validPassword);
        self::assertTrue($valid);
    }

    /**
     * Verify that validPhone() accepts a valid Niger/France phone number.
     *
     * @return void
     */
    public function testValidPhoneInput()
    {
        $field = "phone"; $value = "+22786612016";

        $validPhone = self::getValidator()->validPhone($field,$value);
        $valid = $validPhone->isValid();

        self::assertInstanceOf(Validator::class, $validPhone);
        self::assertTrue($valid);
    }

    /**
     * Verify that validUrl() accepts a well-formed URL.
     *
     * @return void
     */
    public function testValidUrlInput()
    {
        $field = "URL"; $value = "https://example.com";
        
        $validUrl = self::getValidator()->validUrl($field,$value);
        $valid = $validUrl->isValid();
        
        self::assertInstanceOf(Validator::class, $validUrl);
        self::assertTrue($valid);
    }

    /**
     * Verify that unique() accepts a new email not present in the database.
     *
     * @return void
     */
    public function testUniqueValues()
    {
        $table = "users";
        $column = "email";
        $value = self::$uniqueEmail;

        chargerEnv(NS_REP.'.env');
        self::deleteTestUser($value);

        $uniqueValue = self::getValidator()
            ->setDatabase()
            ->unique($table, $column, $value);
        $valid = $uniqueValue->isValid();
        
        self::assertInstanceOf(Validator::class, $uniqueValue);
        self::assertTrue($valid);
    }

    /**
     * Verify that match() succeeds when two values are identical.
     *
     * @return void
     */
    public function testValueMatch()
    {
        $field = "password"; 
        $value = "AzertyQwerty12!!!";
        $match = "AzertyQwerty12!!!";
        
        $validMatch = self::getValidator()->match($field,$value,$match);
        $valid = $validMatch->isValid();
        
        self::assertInstanceOf(Validator::class, $validMatch);
        self::assertTrue($valid);
    }

    // Section for invalid actions test
    
    /**
     * Verify that required() rejects an empty value.
     *
     * @return void
     */
    public function testEmptyRequiredField()
    {
        $field = "email";
        $value = "";

        $required = self::getValidator()->required($field, $value);
        $invalid = $required->isValid();

        self::assertInstanceOf(Validator::class, $required);
        self::assertFalse($invalid);
        self::assertArrayHasKey($field, $required->getErrors());
        self::assertSame("$field est vide. Veuillez le remplir.", $required->getErrors()[$field]);
    }
    
    /**
     * Verify that validName() rejects malformed usernames.
     *
     * @return void
     */
    public function testInvalidNameInput()
    {
        $field = "name"; $value = "incorrectName1";

        $invalidName = self::getValidator()->validName($field,$value);
        $invalid = $invalidName->isValid();

        self::assertInstanceOf(Validator::class, $invalidName);
        self::assertFalse($invalid);
        self::assertArrayHasKey($field, $invalidName->getErrors());
    }
    
    /**
     * Verify that validEmail() rejects invalid email formats.
     *
     * @return void
     */
    public function testInvalidEmailInput()
    {
        $field = "email"; $value = "not-an-email";
        
        $invalidEmail = self::getValidator()->validEmail($field,$value);
        $invalid = $invalidEmail->isValid();
        
        self::assertInstanceOf(Validator::class, $invalidEmail);
        self::assertFalse($invalid);
        self::assertArrayHasKey($field, $invalidEmail->getErrors());
        self::assertSame(MSG_INVALID_EMAIL, $invalidEmail->getErrors()[$field]);
    }

    /**
     * Verify that validPassword() rejects a weak password.
     *
     * @return void
     */
    public function testInvalidePasswordInput()
    {
        $field = "password"; $value = "zertywerty";
        
        $invalidPassword = self::getValidator()->validPassword($field,$value);
        $invalid = $invalidPassword->isValid();
        
        self::assertInstanceOf(Validator::class, $invalidPassword);
        self::assertFalse($invalid);
        self::assertArrayHasKey($field, $invalidPassword->getErrors());
    }

    /**
     * Verify that validPhone() rejects an invalid phone string.
     *
     * @return void
     */
    public function testInvalidPhoneInput()
    {
        $field = "phone"; $value = "+22234T34T";

        $invalidPhone = self::getValidator()->validPhone($field,$value);
        $invalid = $invalidPhone->isValid();

        self::assertInstanceOf(Validator::class, $invalidPhone);
        self::assertFalse($invalid);
        self::assertArrayHasKey($field, $invalidPhone->getErrors());
    }

    /**
     * Verify that validUrl() rejects a malformed URL.
     *
     * @return void
     */
    public function testInvalidUrlInput()
    {
        $field = "URL"; $value = "example.com";
        
        $invalidUrl = self::getValidator()->validUrl($field,$value);
        $invalid = $invalidUrl->isValid();
        
        self::assertInstanceOf(Validator::class, $invalidUrl);
        self::assertFalse($invalid);
        self::assertArrayHasKey($field, $invalidUrl->getErrors());
    }

    /**
     * Verify that unique() rejects a duplicate email in the database.
     *
     * @return void
     */
    public function testDuplicateUniqueValues()
    {
        // if (!self::$hasUsersTable) {
        //     $this->markTestSkipped('Users table missing in test DB.');
        //     return;
        // }

        $table = "users";
        $column = "email";
        $value = self::$duplicateEmail;

        self::ensureTestUser($value);

        $duplicateValue = self::getValidator()
            ->setDatabase()
            ->unique($table, $column, $value);
        $invalid = $duplicateValue->isValid();
        
        self::assertInstanceOf(Validator::class, $duplicateValue);
        self::assertFalse($invalid);
        self::assertArrayHasKey($column, $duplicateValue->getErrors());
    }

    /**
     * Verify that match() rejects non-matching values.
     *
     * @return void
     */
    public function testNonMatchingValues()
    {
        $field = "password"; 
        $value = "AzertyQwerty12!!!";
        $match = "QwertyAzerty12!!!";
        
        $invalidMatch = self::getValidator()->match($field,$value,$match);
        $invalid = $invalidMatch->isValid();
        
        self::assertInstanceOf(Validator::class, $invalidMatch);
        self::assertFalse($invalid);
        self::assertArrayHasKey($field, $invalidMatch->getErrors());
    }
}