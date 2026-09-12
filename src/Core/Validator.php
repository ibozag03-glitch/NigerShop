<?php
/**
 * Ce fichier permet de valider les différents type de connexions 
*/
namespace App\Core;

use InvalidArgumentException;
use RuntimeException;

class Validator {
    
    private array $errors = [];

    /**
     * Set the database for validations that require persistence checks.
     * 
     * @return self for fluent call
     */
    public function setDatabase(): self
    {
        Database::init($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);
        return $this;
    }

    /**
     * Ensure The database is setted
     * 
     * @throws RuntimeException
     * @return void
     */
    public function ensureDatabase(): void
    {
        if (!Database::isConnected()) {
            throw new RuntimeException("Database is not set. Call setDatabase() first.");
        }
    }

    /**
     * Ensure a value is not empty.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value Value to validate
     * @return self
     */
    public function required(string $field, mixed $value): self
    {
        if (trim( (string) $value ) === '') {
            $this->errors[$field]  = "$field est vide. Veuillez le remplir.";
        }
        return $this;
    }

    /**
     * Validate a username using length and pattern rules.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value Username value
     * @return self
     */
    public function validName(string $field, mixed $value): self
    {
        $namelength = strlen( (string) $value );
        $nameFormat = "/^[a-zA-Z][a-zA-Z_-]{2,49}$/";

        if ($namelength < MIN_USERNAME_LEN) {
            $this->errors[$field] = "$field est trop court.";

        } elseif ($namelength > MAX_USERNAME_LEN) {
            $this->errors[$field] = "$field est trop long.";

        } elseif (!preg_match($nameFormat, $value)) {
            $this->errors[$field] = "$field est mal formaté.";
        }

        return $this;
    }

    /**
     * Validate an email address.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value Email value
     * @return self
     */
    public function validEmail(string $field, mixed $value): self
    {
        if (!filter_var((string) $value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = MSG_INVALID_EMAIL;
        }
        return $this;
    }
    
    /**
     * Validate a password by length and complexity rules.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value Password value
     * @return self
     */
    public function validPassword(string $field, mixed $value): self
    {
        $validPWD = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/";
        $pwdlength = strlen((string)$value);

        if ($pwdlength < MIN_PWD_LEN) {
            $this->errors[$field] = MSG_ERREUR_PWD_COURT;
        } elseif (!preg_match($validPWD, $value)) {
            $this->errors[$field] = MSG_INVALID_PWD;
        }

        return $this;
    }

    /**
     * Validate a phone number for Niger or France formats.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value Phone value
     * @return self
     */
    public function validPhone(string $field, mixed $value): self
    {
        $formatNe = "/^(\+227)[\s.-]?(\d{8}|\d{2}[\s.-]?\d{2}[\s.-]?\d{2}[\s.-]?\d{2}[\s.-]?)$/";
        $formatFr = "/^(\+33|0)[1-9](\d{8}|\d{2}[\s.-]?\d{2}[\s.-]?\d{2}[\s.-]?\d{2}[\s.-]?)$/";
        
        if (!(preg_match($formatNe, (string) $value) || preg_match($formatFr, (string) $value))) {
            $this->errors[$field] = "$field n'est pas un numéro valide.";
        }
        return $this;
    }

    /**
     * Validate that a value is unique in the database.
     *
     * @param string $table Table name
     * @param string $column Column name
     * @param mixed $value Value to check
     * @return self
     */
    public function unique(string $table, string $column, mixed $value): self
    {
        $uniqueformat = "/^[a-zA-Z0-9_]+$/";
        if (!preg_match($uniqueformat, (string) $table) || !preg_match($uniqueformat, (string) $column)) {
            throw new InvalidArgumentException("Invalid table or Column name.");
        }

        $this->ensureDatabase();
        
        $verifSQL = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = :value";
        $result = Database::fetch($verifSQL, ["value" => $value]);

        if ($result['count'] > 0) {
            $this->errors[$column] = "Ce $column existe déjà.";
        }
        return $this;
    }
    
    /**
     * Validate a URL.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value URL value
     * @return self
     */
    public function validUrl(string $field, mixed $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->errors[$field] = "$field n'est pas valide."; 
        }
        return $this;
    }

    /**
     * Validate that two values match.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value First value
     * @param mixed $match Expected matching value
     * @return self
     */
    public function match(string $field, mixed $value, mixed $match): self
    {
        if ($value !== $match) {
            $this->errors[$field] = "$field ne correspond pas.";
        }
        return $this;
    }
    
    /**
     * Validate that two values don't match
     * 
     * @param string $field Field name used for error messages
     * @param mixed $value1 First value
     * @param mixed $value2 Second value
     * @return self
     */
    public function dontMatch(string $field, mixed $value1, mixed $value2): self
    {
        if ($value1 === $value2) {
            $this->errors[$field] = "les $field sont identique.";
        }
        return $this;
    }
    
    /**
     * Validate minimum string length.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value Value to validate
     * @param int $length Minimum length
     * @return self
     */
    public function minLength(string $field, mixed $value, int $length): self
    {
        if (strlen( (string) $value) < $length) {
            $this->errors[$field] = "$field est trop court. Il doit avoir au moins $length caractères.";    
        }
        return $this;
    }

    /**
     * Validate maximum string length.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value Value to validate
     * @param int $length Maximum length
     * @return self
     */
    public function maxLength(string $field, mixed $value, int $length): self
    {
        if (strlen( (string) $value) > $length) {
            $this->errors[$field] = "$field est trop long. Il doit avoir au plus $length caractères.";
        }
        return $this;
    }

    /**
     * Validate that the value is numeric.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value Value to validate
     * @return self
     */
    public function isNumber(string $field, mixed $value): self
    {
        if (!is_numeric($value)) {
            $this->errors[$field] = "$field n'est pas une valeur numerique.";   
        }
        return $this;
    }

    /**
     * Validate that the value is an integer.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value Value to validate
     * @return self
     */
    public function isInteger(string $field, mixed $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[$field] = "$field n'est pas une valeur entière.";    
        }
        return $this;
    }
    
    /**
     * Validate that the value is positive
     * 
     * @param $field Field name used for error message
     * @param mixed $value Value to validate
     * @return self
     */
    public function isPositive(string $field, mixed $value): self
    {
        if (!is_numeric($value)) {
            $this->errors[$field] = "$field n'est pas un nombre";
            return $this;
        }

        if ($value <= 0) {
            $this->errors[$field] = "$field doit être une valeur positive";
        }
        return $this;
    }

    /**
     * Validate that the value represents a boolean.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value Value to validate
     * @return self
     */
    public function isBoolean(string $field, mixed $value): self
    {
        $valid = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($valid === null) {
            $this->errors[$field] = "$field n'est pas une valeur booléen.";
        }
        return $this;
    }

    /**
     * Validate user registration or update data.
     *
     * @param string $usernameField Username field name
     * @param string $emailField Email field name
     * @param string $passwordField Password field name
     * @param array $data Input data array
     * @param bool $createUser Whether the password is required
     * @return self
     */
    public function validUser(string $usernameField, string $emailField, string $passwordField, array $data, bool $createUser = true): self
    {
        $this->required($usernameField, $data[$usernameField] ?? '');
        $this->validName($usernameField, $data[$usernameField] ?? '');
        $this->unique("users", $usernameField, $data[$usernameField] ?? '');
        
        $this->required($emailField, $data[$emailField] ?? '');
        $this->validEmail($emailField, $data[$emailField] ?? '');
        $this->unique("users", $emailField, $data[$emailField] ?? '');
        
        if ($createUser) {
            $this->required($passwordField, $data[$passwordField] ?? '');
            $this->validPassword($passwordField, $data[$passwordField] ?? '');
        }
        
        return $this;
    }

    /**
     * Validate category input fields.
     * 
     * @param string $name Field name for category name
     * @param string $slug Field name for category slug
     * @param string $description Field name for category description
     * @param array $data Input data array
     */
    public function validCategory(string $name, string $slug, string $description, array $data): self
    {
        $this->required($name, $data[$name] ?? '');
        $this->minLength($name, $data[$name] ?? '', 5);
        $this->maxLength($name, $data[$name] ?? '', 25);
        $this->unique("categories", $name, $data[$name] ?? '');

        $this->required($slug, $data[$slug] ?? '');
        $this->minLength($name, $data[$name] ?? '', 5);
        $this->maxLength($slug, $data[$slug] ?? '', 255);
        $this->unique("categories", $slug, $data[$slug] ?? '');

        $this->required($description, $data[$description] ?? '');
        $this->maxLength($description, $data[$description] ?? '', 500);

        return $this;
    }

    /**
     * Validate that a category ID exists in the categories table.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value Category ID
     * @return self
     */
    public function checkCategory(string $field, int $value): self
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->errors[$field] = "La $field est invalide";
            return $this;
        }

        $this->ensureDatabase();

        $searchSQL = "SELECT COUNT(*) as count FROM categories WHERE category_id = :id";
        $result = Database::fetchColumn($searchSQL, ["id" => $value]);

        if ($result === false || (int) $result === 0) {
            $this->errors[$field] = "cette $field n'existe pas.";
        }

        return $this;
    }

    /**
     * Validate annonce input fields.
     *
     * @param string $titre Field name for title
     * @param string $description Field name for description
     * @param string $prix Field name for price
     * @param array $data Input data array
     * @return self
     */
    public function validAnnonce(string $titre, string $description, string $prix, array $data): self
    {
        $this->required($titre, $data[$titre] ?? '');
        $this->minLength($titre, $data[$titre] ?? '', MIN_ANNONCE_TITRE_LEN);
        $this->maxLength($titre, $data[$titre] ?? '', MAX_ANNONCE_TITRE_LEN);

        $this->required($description, $data[$description] ?? '');
        $this->minLength($description, $data[$description] ?? '', MIN_ANNONCE_DESC_LEN);
        $this->maxLength($description, $data[$description] ?? '', MAX_ANNONCE_DESC_LEN);

        $this->required($prix, $data[$prix] ?? '');
        $this->isNumber($prix, $data[$prix] ?? '');
        $this->isPositive($prix, $data[$prix] ?? '');

        return $this;
    }

    /**
     * Validate that a specific annonce ID exists in the annonce table.
     *
     * @param string $field Field name used for error messages
     * @param mixed $value Category ID
     * @return self
     */
    public function checkAnnonce(string $field, mixed $value): self
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->errors[$field] = "La $field est invalide";
            return $this;
        }

        $this->ensureDatabase();

        $searchSQL = "SELECT COUNT(*) as count FROM annonces WHERE annonce_id = :id";
        $result = Database::fetchColumn($searchSQL, ["id" => $value]);

        if ($result === false || (int) $result === 0) {
            $this->errors[$field] = "cette $field n'existe pas.";
        }

        return $this;
    }

    /**
     * Valid discussion information
     * 
     * @param string $annonceId Field name for annonce identifier
     * @param string $customerId Field name for customer identifier
     * @param string $sellerId Field name for seller identifier
     * @param string $status Field name for discussion status
     * @param array $data Contains the actual identifier
     * @return self
     */
    public function validDiscussion(
        string $annonceId, string $customerId, string $sellerId, string $status, array $data): self
    {
        $this->required($annonceId, $data[$annonceId] ?? "");
        $this->isInteger($annonceId, $data[$annonceId] ?? "");
        $this->isPositive($annonceId, $data[$annonceId] ?? "");
        
        $this->required($customerId, $data[$customerId] ?? "");
        $this->isInteger($customerId, $data[$customerId] ?? "");
        $this->isPositive($customerId, $data[$customerId] ?? "");
        
        $this->required($sellerId, $data[$sellerId] ?? "");
        $this->isInteger($sellerId, $data[$sellerId] ?? "");
        $this->isPositive($sellerId, $data[$sellerId] ?? "");
        
        $this->required($status, $data[$status] ?? "");

        $this->dontMatch("users id", $data[$customerId] ?? null, $data[$sellerId] ?? null);

        $allowedStatus = ["active", "closed", "blocked"];
        if (!in_array($data[$status] ?? null, $allowedStatus, true)) {
            $this->errors[$status] = "Le status n'est pas un status invalid";
        }

        return $this;
    }

    /**
     * Validate that a specific discussion id exist in the discussion table
     * 
     * @param string $field Field name used for error messages
     * @param mixed $value Category ID
     * @return self
     */
    public function checkDiscussion(string $field, mixed $value): self
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->errors[$field] = "La $field est invalide";
            return $this;
        }

        $this->ensureDatabase();

        $searchSQL = "SELECT COUNT(*) as count FROM discussions WHERE discussion_id = :id";
        $result = Database::fetchColumn($searchSQL, ["id" => $value]);

        if ($result === false || (int) $result === 0) {
            $this->errors[$field] = "cette $field n'existe pas.";
        }

        return $this;
    }

    /**
     * Checks whether the current validation has no errors.
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Get validation errors.
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Clear stored validation errors.
     * 
     * @return self
     */
    public function clearErrors(): self
    {
        $this->errors = [];
        return $this;
    }
}