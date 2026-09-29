<?php
/**
 * This file handle users discussions operations and validation
*/
namespace App\Models;

use App\Core\Database;
use App\Core\Validator;
use Exception;

class Discussion {

    private const TABLE = "discussions";
    private ?int $discussion_id = null;
    private string $creation_date;
    private string $modification_date;
    private ?int $annonce_id = null;
    private ?int $customer_id = null;
    private ?int $seller_id = null;
    private string $status = "active";
    private ?string $last_message_at = null;

    /**
     * Build a discussion object from a database row
     * 
     * @param array $row Data returned by the discussion table
     * @return self A discussion instance filled with table values
     */
    public static function fromArray(array $row): Discussion
    {
        $discussion = new self();
        $discussion->discussion_id = (int)$row['discussion_id'] ?? null;
        $discussion->creation_date = $row['creation_date'] ?? "";
        $discussion->modification_date = $row['modification_date'] ?? "";
        $discussion->annonce_id = (int)$row['annonce_id'] ?? null;
        $discussion->customer_id = (int)$row['customer_id'] ?? null;
        $discussion->seller_id = (int)$row['seller_id'] ?? null;
        $discussion->status = $row['status'] ?? "active";
        $discussion->last_message_at = $row['last_message_at'] ?? null;

        return $discussion;
    }

    /**
     * Get a discussion by its identifier
     * 
     * @param $discussion_id The discussion identifier
     * @return self|null The discussion records if it exist, otherwise null
     */
    public static function findById(int $discussion_id): ?self
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE discussion_id = :id";
        $params = [
            "id" => $discussion_id
        ];
        $results = Database::fetch($sql, $params);

        return ($results) ? self::fromArray($results) : null;
    }
    
    /**
     * Get all discussions for a specific annonce
     * 
     * @param $annonce_id Annonce indentifier
     * @return array List of discussion found for the annonce
     */
    public static function findByAnnonceId(int $annonce_id): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE annonce_id = :id";
        $params = [
            "id" => $annonce_id
        ];
        $results = Database::fetchAll($sql, $params);

        $discussions = [];
        foreach ($results as $result) {
            $discussions[] = self::fromArray($result);
        }

        return $discussions;
    }

    /**
     * Get all discussions for a specific customer
     * 
     * @param int $customer_id Customer identifier
     * @return array List of discussion found for the customer
     */
    public static function findByCustomerId(int $customer_id): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE customer_id = :id";
        $params = [
            "id" => $customer_id
        ];
        $results = Database::fetchAll($sql, $params);

        $discussions = [];
        foreach ($results as $result) {
            $discussions[] = self::fromArray($result);
        }

        return $discussions;
    }
    
    /**
     * Get all discussions for a specific seller
     * 
     * @param int $seller_id Seller identifier
     * @return array List of discussion found for the seller
     */
    public static function findBySellerId(int $seller_id): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE seller_id = :id";
        $params = [
            "id" => $seller_id
        ];
        $results = Database::fetchAll($sql, $params);

        $discussions = [];
        foreach ($results as $result) {
            $discussions[] = self::fromArray($result);
        }

        return $discussions;
    }
    
    /**
     * Get all discussions
     * 
     * @return array The complete list of all discussions in the database
     */
    public static function findAll(): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " ORDER BY creation_date DESC";
        $results = Database::fetchAll($sql);

        $discussions = [];
        foreach ($results as $result) {
            $discussions[] = self::fromArray($result);
        }

        return $discussions;
    }

    /**
     * Create a discussion in the database
     * 
     * @throws Exception If annonce, customer and seller ids are missing
     * @return bool True on success
     */

    public function create(): bool
    {
        $validator = new Validator();
        $validator->validDiscussion("annonce_id", "customer_id", "seller_id", "status", [
            "annonce_id" => $this->annonce_id,
            "customer_id" => $this->customer_id,
            "seller_id" => $this->seller_id,
            "status" => $this->status
        ]);
        
        if (!$validator->isValid()) {
            throw new Exception("VALIDATION ERROR : " . implode("; ", $validator->getErrors()));
        }

        $this->creation_date = date("Y-m-d H:i:s");
        $this->modification_date = date("Y-m-d H:i:s");

        $sql = "INSERT INTO " . self::TABLE . " 
        (creation_date, modification_date, annonce_id, customer_id, seller_id, status) 
        VALUES (:creation_date, :modification_date, :annonce_id, :customer_id, :seller_id, :status)";
        $params = [
            "creation_date" => $this->creation_date,
            "modification_date" => $this->modification_date,
            "annonce_id" => $this->annonce_id,
            "customer_id" => $this->customer_id,
            "seller_id" => $this->seller_id,
            "status" => $this->status
        ];

        Database::query($sql, $params);

        $this->discussion_id = (int)Database::lastInsertId();
        return true;
    }

    /**
     * Update a discussion in the database
     * 
     * @throws Exception If discussion, annonce, customer and seller ids are missing
     * @return bool True on success
     */
    public function update(): bool
    {
        $validator = new Validator();
        $validator->checkDiscussion("discussion id", $this->discussion_id)
            ->validDiscussion("annonce_id", "customer_id", "seller_id", "status", [
            "annonce_id" => $this->annonce_id,
            "customer_id" => $this->customer_id,
            "seller_id" => $this->seller_id,
            "status" => $this->status
        ]);

        if (!$validator->isValid()) {
            throw new Exception("VALIDATION ERROR : " . implode("; ", $validator->getErrors()));
        }

        $this->modification_date = date("Y-m-d H:i:s");

        $sql = "UPDATE " . self::TABLE . " SET 
            modification_date = :modification_date,
            annonce_id = :annonce_id,
            customer_id = :customer_id,
            seller_id = :seller_id,
            status = :status
            WHERE discussion_id = :id
        ";
        $params = [
            "id" => $this->discussion_id,
            "modification_date" => $this->modification_date,
            "annonce_id" => $this->annonce_id,
            "customer_id" => $this->customer_id,
            "seller_id" => $this->seller_id,
            "status" => $this->status
        ];

        Database::query($sql, $params);

        return true;
    }

    /**
     * Delete a discussion in the database
     * 
     * @throws Exception If discussion id is missing
     * @return bool True on success
     */
    public function delete(): bool
    {
        $validator = new Validator();
        $validator->required("discussion id", $this->discussion_id);

        if (!$validator->isValid()) {
            throw new Exception("DELETION FAIL : the discussion doesn't exist");
        }

        $sql = "DELETE FROM " . self::TABLE . " WHERE discussion_id = :id";
        $params = ["id" => $this->discussion_id];

        $statement = Database::query($sql, $params);

        if ($statement->rowCount() === 0) {
            throw new Exception("DELETION FAIL : the discussion doesn't exist");
        }

        return true;
    }

    /**
     * Set an identifier for the discussion
     * 
     * @param $discussion_id Discussion identifier to set
     * @return self The current instance
     */
    public function setDiscussionId(int $discussion_id): self
    {
        $this->discussion_id = $discussion_id;
        return $this;
    }

    /**
     * Set an identifier for the discussion annonce
     * 
     * @param int $annonce_id Annonce identifier to set
     * @return self The current instance
     */
    public function setAnnonceId(int $annonce_id): self
    {
        $this->annonce_id = $annonce_id;
        return $this;
    }

    /**
     * Set an identifier for the discussion customer
     * 
     * @param int $customer_id Customer identifier to set
     * @return self The current instance
     */
    public function setCustomerId(int $customer_id): self
    {
        $this->customer_id = $customer_id;
        return $this;
    }

    /**
     * Set an identifier for the discussion seller
     * 
     * @param int $seller_id Seller identifier to set
     * @return self The current instance
     */
    public function setSellerId(int $seller_id): self
    {
        $this->seller_id = $seller_id;
        return $this;
    }

    /**
     * Set a discussion status
     * 
     * @param string $status The status to set
     * @return self The current instance
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get a discussion identifier
     *
     * @return int|null The discussion identifier if it exist and null if not
     */
    public function getDiscussionId(): ?int { return $this->discussion_id; }
    
    /**
     * Get the discussion annonce identifier
     * 
     * @return int|null The annonce identifier if it exist and null if not
     */
    public function getAnnonceId(): ?int { return $this->annonce_id; }

    /**
     * Get the discussion customer identifier
     * 
     * @return int|null The customer identifier if it exist and null if not
     */
    public function getCustomerId(): ?int { return $this->customer_id; }
    
    /**
     * Get the discussion seller identifier
     * 
     * @return int|null The seller identifier if it exist and null if not
     */
    public function getSellerId(): ?int { return $this->seller_id; }

    /**
     * Get the discussion status
     * 
     * @return string|null The dscussion status
     */
    public function getStatus(): ?string { return $this->status; }

    /**
     * Get the discussion status
     * 
     * @return string|null The dscussion last message send time
     */
    public function getLastMessageAt(): ?string { return $this->last_message_at; }
    
}