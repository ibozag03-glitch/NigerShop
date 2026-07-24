<?php
require_once dirname(__Dir__) . DIRECTORY_SEPARATOR . 'config/config.php';

use App\Core\Database;
use PHPUnit\Framework\TestCase;
use App\Core\Validator;

final class ValidatorTest extends TestCase
{
    public function getValidator(): Validator
    {
        return new Validator();
    }

    public function testRequiredField($field = "email", $value = "test_email@gmail.c")
    {
        $required = self::getValidator()->required($field, $value);
        $valid = $required->isValid();
        self::assertInstanceOf(Validator::class,$required);
        self::assertTrue($valid);
    }

    public function testValidNameInput($field = "name", $value = "ibozag")
    {
        $validName = self::getValidator()->validName($field,$value);
        $valid = $validName->isValid();
        self::assertInstanceOf(Validator::class, $validName);
        self::assertTrue($valid);
    }

    public function testValidEmailInput($field = "email", $value = "testemail@gmail.c")
    {
        $validEmail = self::getValidator()->validName($field,$value);
        $valid = $validEmail->isValid();
        self::assertInstanceOf(Validator::class, $validEmail);
        self::assertTrue($valid);
    }

    public function testValidPasswordInput($field = "password", $value = "AzertyQwerty12!!!")
    {
        $validPassword = self::getValidator()->validPassword($field,$value);
        $valid = $validPassword->isValid();
        self::assertInstanceOf(Validator::class,$validPassword);
        self::assertTrue($valid);
    }

    public function testValidPhoneInput($field = "phone", $value = "+22786612016")
    {
        $validPhone = self::getValidator()->validPhone($field,$value);
        $valid = $validPhone->isValid();
        self::assertInstanceOf(Validator::class,$validPhone);
        self::assertTrue($valid);
    }

    public function testValidUrl($field = "URL", $value = "something")
    {
        $validUrl = self::getValidator()->validUrl($field,$value);
        $valid = $validUrl->isValid();
        self::assertInstanceOf(Validator::class,$validUrl);
        self::assertTrue($valid);
    }

    public function testUniqueValues($table = "users", $column = "user_id", $value = "2")
    {
        chargerEnv(NS_REP.'.env');

        $uniqueValue = self::getValidator()
            ->setDatabase(new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PWD"]))
            ->unique($table,$column,$value);
        $valid = $uniqueValue->isValid();
        self::assertInstanceOf(Validator::class,$uniqueValue);
        self::assertTrue($valid);
    }

    public function testValueMatch($field = "password", $value = "AzertyQwerty12!!!", $match = "QwertyAzerty12!!!")
    {
        $validMatch = self::getValidator()->match($field,$value,$match);
        $valid = $validMatch->isValid();
        self::assertInstanceOf(Validator::class,$validMatch);
        self::assertTrue($valid);
    }
}