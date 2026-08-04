<?php
/**
 * This file handle user data operation and validation
*/
namespace App\Models;

use App\Core\Database;
use App\Core\Validator;
use Exception;

class User {

    private const TABLE = "users";
    private int $user_id;
    private string $firstname;
    private string $lastname;
    private string $email;
    private string $password;
    private string $address;
    private string $phone;
    private string $birth_date;
    private string $role = "user";
    private string $inscription_date;
    private string $modification_date;
    private string $status = "active";

    public function fromArray(array $row): self
    {
        $user = new self();
        self::$user_id = $row['user_id'] ?? null;
        self::$firstname = $row['firstname'] ?? "";
        self::$lastname = $row['lastname'] ?? "";
        self::$email = $row['email'] ?? "";
        self::$password = $row['password'] ?? "";
        self::$address = $row['address'] ?? "";
        self::$phone = $row['phone'] ?? "";
        self::$birth_date = $row['birth_date'] ?? "";
        self::$role = $row['role'] ?? "user";
        self::$inscription_date = $row['inscription_date'] ?? "";
        self::$modification_date = $row['modification_date'] ?? "";
        self::$status = $row['status'] ?? "active";

        return $user;
    }

    public function setUserId(int $user_id): self
    {
        self::$user_id = $user_id;
        return $this;
    }
    
    public function setFirstname(string $firstname): self
    {
        self::$firstname = $firstname;
        return $this;
    }
    
    public function setLastname(string $lastname): self
    {
        self::$lastname = $lastname;
        return $this;
    }
    
    public function setEmail(string $email): self
    {
        self::$email = $email;
        return $this;
    }
    
    public function setPassword(string $password): self
    {
        self::$password = $password;
        return $this;
    }
    
    public function setAddress(string $address): self
    {
        self::$address = $address;
        return $this;
    }
    
    public function setPhone(string $phone): self
    {
        self::$phone = $phone;
        return $this;
    }
    
    public function setBirhtDate(string $birth_date): self
    {
        self::$birth_date = $birth_date;
        return $this;
    }
    
    public function setRole(string $role = "user"): self
    {
        self::$role = $role;
        return $this;
    }
    
    public function setInscriptionDate(string $inscription_date): self
    {
        self::$inscription_date = $inscription_date;
        return $this;
    }
    
    public function setModificationDate(string $modification_date): self
    {
        self::$modification_date = $modification_date;
        return $this;
    }
    
    public function setStatus(string $status = "active"): self
    {
        self::$status = $status;
        return $this;
    }
    
    public function getUserId(): ?int { return self::$user_id; }
    public function getFirstname(): string { return self::$firstname; }
    public function getLastname(): string { return self::$lastname; }
    public function getEmail(): string { return self::$email; }
    public function getPassword(): string { return self::$password; }
    public function getAddress(): string { return self::$address; }
    public function getPhone(): string { return self::$phone; }
    public function getBirthDate(): ?string { return self::$birth_date; }
    public function getRole(): string { return self::$role; }
    public function getInscriptionDate(): ?string { return self::$inscription_date; }
    public function getModificationDate(): ?string { return self::$modification_date; }
    public function getStatus(): string { return self::$status; }

    public static function findById(int $user_id): ?self
    {
        try {
            
            $sql = "SELECT * FROM " . self::TABLE . " WHERE user_id = :id";
            $param = ["id" => $user_id];
            $result = Database::fetch($sql, $param);

        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }

        return $result ? self::fromArray($result) : null;
    }

    public static function findByEmail(string $email): ?self
    {
        try {
        
            $sql = "SELECT * FROM " . self::TABLE . " WHERE email = :email";
            $param = ["email" => $email];
            $result = Database::fetch($sql, $param);

        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }

        return $result ? self::fromArray($result) : null;
    }
    
    public static function findAll(): array
    {
        try {
            
            $sql = "SELECT * FROM " . self::TABLE . " ORDER BY user_id";
            $results = Database::fetchAll($sql);

            $users = [];
            foreach ($results as $result) {
                $users[] = self::fromArray($result);
            }

        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }

        return $users;
    }

    public function create(): bool
    {
        $validator = new Validator();
        $validator->validUser("firstname", "email", "password", [
            "firstname" => self::$firstname,
            "email" => self::$email,
            "password" => self::$password
        ], true);

        if (!$validator->isValid()) {
            throw new Exception("VALIDATION ERROR : " . implode('; ', $validator->getErrors()));
        }

        $sql = "INSERT INTO " . self::TABLE . " 
        (firstname, lastname, email, password, address, phone, birth_date, role, status, inscription_date, modification_date) 
        VALUES 
        (:firstname, :lastname, :email, :password, :address, :phone, :birth_date, :role, :status, :inscription_date, :modification_date)";
        
        $params = [
            "firstname" => self::$firstname,
            "lastname" => self::$lastname,
            "email" => self::$email,
            "password" => password_hash(self::$password, PASSWORD_DEFAULT),
            "address" => self::$address,
            "phone" => self::$phone,
            "birth_date" => self::$birth_date,
            "role" => self::$role,
            "status" => self::$status,
            "inscription_date" => self::$inscription_date,
            "modification_date" => self::$modification_date,
        ];

        Database::query($sql, $params);
        
        self::$user_id = (int)Database::lastInsertId();

        return true;
    }

    public function update(): bool
    {
        if (self::$user_id === null) {
            throw new Exception("FAIL to update : user doesn't exist.");
        }

        self::$modification_date = date("Y-m-d H:i:s");

        $sql = "UPDATE " . self::TABLE . " SET 
            firstname = :firstname,
            lastname = :lastname,
            email = :email,
            password = :password,
            address = :address,
            phone = :phone,
            birth_date = :birth_date,
            role = :role,
            status = :status,
            modification_date = :modification_date
            WHERE user_id = :id";
        
        $params = [
            "id" => self::$user_id,
            "firstname" => self::$firstname,
            "lastname" => self::$lastname,
            "email" => self::$email,
            "password" => password_hash(self::$password, PASSWORD_DEFAULT),
            "address" => self::$address,
            "phone" => self::$phone,
            "birth_date" => self::$birth_date,
            "role" => self::$role,
            "status" => self::$status,
            "modification_date" => self::$modification_date,
        ];

        Database::query($sql, $params);
        
        return true;
    }

    public function delete(): bool
    {
        if (self::$user_id === null) {
            throw new Exception("FAIL to delete : user doesn't exist");
        }
        
        $sql = "DELETE FROM " . self::TABLE . " WHERE user_id = :id";
        $params = ["id" => self::$user_id];

        Database::query($sql, $params);

        return true;
    }

    public function save(): bool
    {
        self::$modification_date = date("Y-m-d H:i:s");

        if (self::$user_id !== null) {
            return $this->update();
        } else {
            if (self::$inscription_date === null) {
                self::$inscription_date = date("Y-m-d H:i:s");
            }
            return $this->create();
        }
    }
    
}