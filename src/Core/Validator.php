<?php
/**
 * Ce fichier permet de valider les différents type de connexions 
*/
namespace App\Core;

require_once __DIR__ . '/../../config/config.php';

class Validator {
    
    private array $errors = [];
    private Database $database;

    public function setDatabase(Database $database)
    { 
        $this->database = $database;   
    }

    public function required(string $field, mixed $value): self
    {
        if(empty(trim((string)$value))){
            $this->errors[$field]  = "$field est vide. Veuillez le remplir.";
        }
        return $this;
    }
    
    public function minLength(string $field, mixed $value, int $length): self
    {
        if(strlen((string)$value) < $length){
            $this->errors[$field] = "$field est trop court. Il doit avoir au moins $length caractères.";    
        }
        return $this;
    }

    public function maxLength(string $field, mixed $value, int $length): self
    {
        if(strlen((string)$value) > $length){
            $this->errors[$field] = "$field est trop long. Il doit avoir au plus $length caractères.";
        }
        return $this;
    }

    public function isNumber(string $field, mixed $value): self
    {
        if(!is_numeric($value)){
            $this->errors[$field] = "$field n'est pas une valeur numerique.";   
        }
        return $this;
    }

    public function isInteger(string $field, mixed $value): self
    {
        if(!filter_var($value, FILTER_VALIDATE_INT)){
            $this->errors[$field] = "$field n'est pas une valeur entière.";    
        }
        return $this;
    }

    public function isBoolean(string $field, mixed $value): self
    {
        if(!filter_var($value, FILTER_VALIDATE_BOOLEAN)){
            $this->errors[$field] = "$field n'est pas une valeur booléen.";
        }
        return $this;
    }
    
    public function validUrl(string $field, mixed $value): self
    {
        if(!filter_var($value, FILTER_VALIDATE_URL)){
            $this->errors[$field] = "$field n'est pas une URL valide."; 
        }
        return $this;
    }
    
    public function validPhone(string $field, mixed $value): self
    {
        $formatNe = "/^(\+227)[\s.-]?(\d{8}|\d{2}[\s.-]?\d{2}[\s.-]?\d{2}[\s.-]?\d{2}[\s.-]?)$/";
        $formatFr = "/^(\+33|0)[1-9](\d{8}|\d{2}[\s.-]?\d{2}[\s.-]?\d{2}[\s.-]?\d{2}[\s.-]?)$/";
        
        if(!(preg_match($formatNe, $value) || preg_match($formatFr, $value))){
            $this->errors[$field] = "$field n'est pas un numéro valide.";
        }
        return $this;
    }

    public function match(string $field, mixed $value, mixed $match): self
    {
        if($value !== $match){
            $this->errors[$field] = "$field ne correspond pas.";
        }
        return $this;
    }

    public function validName(string $field, mixed $value): self
    {
        $namelength = strlen($value);
        // Accepte majuscule OU minuscule au début, suivi de 2-29 caractères
        $nameFormat = "/^[a-zA-Z][a-zA-Z_-]{2,29}$/";

        if($namelength < MIN_USERNAME_LEN){
            $this->errors[$field] = "$field est trop court.";

        }elseif($namelength > MAX_USERNAME_LEN){
            $this->errors[$field] = "$field est trop long.";

        }elseif(!preg_match($nameFormat,$value)){
            $this->errors[$field] = "$field est mal formaté.";
        }

        return $this;
    }

    public function validEmail(string $field, mixed $value): self
    {
        $emailFormat = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z]+\.[a-zA-Z]{2,}$/i';
        if(!preg_match($emailFormat, $value)){
            $this->errors[$field] = MSG_INVALID_EMAIL;
        }
        return $this;
    }
    
    public function validPassword(string $field, mixed $value): self
    {
        $validPWD = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/i";
        $pwdlength = strlen($value);

        if($pwdlength < MIN_PWD_LEN){
            $this->errors[$field] = MSG_ERREUR_PWD_COURT;
        }elseif(!preg_match($validPWD,$value)){
            $this->errors[$field] = MSG_INVALID_PWD;
        }

        return $this;
    }

    public function unique(string $table, string $column, mixed $value): self
    {
        $verifSQL = "SELECT COUNT(*) as count FROM $table WHERE $column = :value";
        $result = $this->database->fetch($verifSQL, ["value" => $value]);

        if ($result['count'] > 0){
            $this->errors[$column] = "Ce $column existe déjà.";
        }
        return $this;
    }

    public function validCategory(string $field, mixed $value): self
    {
        if(filter_var($value, FILTER_VALIDATE_INT) === false){
            $this->errors[$field] = "La $field est invalide";
            return $this;
        }

        $searchSQL = "SELECT COUNT(*) as count FROM categories WHERE id_categorie = :id";
        $result = $this->database->fetchColumn($searchSQL,["id" => $value]);

        if($result['count'] === 0){
            $this->errors[$field] = "cette $field n'existe pas.";
        }

        return $this;
    }

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

        return $this;
    }

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

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function clearErrors(): self
    {
        $this->errors = [];
        return $this;
    }
}