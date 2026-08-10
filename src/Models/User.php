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
            "firstname" => $this->firstname,
            "email" => $this->email,
            "password" => $this->password
        ], true);

        if (!$validator->isValid()) {
            throw new Exception("VALIDATION ERROR : " . implode('; ', $validator->getErrors()));
        }

        $sql = "INSERT INTO " . self::TABLE . " 
        (firstname, lastname, email, password, address, phone, birth_date, role, status, inscription_date, modification_date) 
        VALUES 
        (:firstname, :lastname, :email, :password, :address, :phone, :birth_date, :role, :status, :inscription_date, :modification_date)";
        
        $params = [
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "email" => $this->email,
            "password" => password_hash($this->password, PASSWORD_DEFAULT),
            "address" => $this->address,
            "phone" => $this->phone,
            "birth_date" => $this->birth_date,
            "role" => $this->role,
            "status" => $this->status,
            "inscription_date" => $this->inscription_date,
            "modification_date" => $this->modification_date,
        ];

        Database::query($sql, $params);
        
        $this->user_id = (int)Database::lastInsertId();

        return true;
    }

    public function update(): bool
    {
        if ($this->user_id === null) {
            throw new Exception("FAIL to update : user doesn't exist.");
        }

        $this->modification_date = date("Y-m-d H:i:s");

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
            "id" => $this->user_id,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "email" => $this->email,
            "password" => password_hash($this->password, PASSWORD_DEFAULT),
            "address" => $this->address,
            "phone" => $this->phone,
            "birth_date" => $this->birth_date,
            "role" => $this->role,
            "status" => $this->status,
            "modification_date" => $this->modification_date,
        ];

        Database::query($sql, $params);
        
        return true;
    }

    public function delete(): bool
    {
        if ($this->user_id === null) {
            throw new Exception("FAIL to delete : user doesn't exist");
        }
        
        $sql = "DELETE FROM " . self::TABLE . " WHERE user_id = :id";
        $params = ["id" => $this->user_id];

        Database::query($sql, $params);

        return true;
    }

    public function save(): bool
    {
        $this->modification_date = date("Y-m-d H:i:s");

        if ($this->user_id !== null) {
            return $this->update();
        } else {
            if ($this->inscription_date === null) {
                $this->inscription_date = date("Y-m-d H:i:s");
            }
            return $this->create();
        }
    }

    public function fromArray(array $row): self
    {
        $user = new self();
        $user->user_id = $row['user_id'] ?? null;
        $user->firstname = $row['firstname'] ?? "";
        $user->lastname = $row['lastname'] ?? "";
        $user->email = $row['email'] ?? "";
        $user->password = $row['password'] ?? "";
        $user->address = $row['address'] ?? "";
        $user->phone = $row['phone'] ?? "";
        $user->birth_date = $row['birth_date'] ?? "";
        $user->role = $row['role'] ?? "user";
        $user->inscription_date = $row['inscription_date'] ?? "";
        $user->modification_date = $row['modification_date'] ?? "";
        $user->status = $row['status'] ?? "active";

        return $user;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }
    
    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }
    
    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }
    
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
    
    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }
    
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }
    
    public function setBirthDate(string $birth_date): self
    {
        $this->birth_date = $birth_date;
        return $this;
    }
    
    public function setRole(string $role = "user"): self
    {
        $this->role = $role;
        return $this;
    }
    
    public function setInscriptionDate(string $inscription_date): self
    {
        $this->inscription_date = $inscription_date;
        return $this;
    }
    
    public function setModificationDate(string $modification_date): self
    {
        $this->modification_date = $modification_date;
        return $this;
    }
    
    public function setStatus(string $status = "active"): self
    {
        $this->status = $status;
        return $this;
    }
    
    public function getUserId(): ?int { return $this->user_id; }
    public function getFirstname(): string { return $this->firstname; }
    public function getLastname(): string { return $this->lastname; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getAddress(): string { return $this->address; }
    public function getPhone(): string { return $this->phone; }
    public function getBirthDate(): ?string { return $this->birth_date; }
    public function getRole(): string { return $this->role; }
    public function getInscriptionDate(): ?string { return $this->inscription_date; }
    public function getModificationDate(): ?string { return $this->modification_date; }
    public function getStatus(): string { return $this->status; }
    
}