<?php
/**
 * This file handle user data operation and validation
*/
namespace App\Models;

use App\Core\Database;
use App\Core\Validator;
use Exception;

class Annonce {

    const string TABLE = "annonces";
    private ?int $annonce_id;
    private ?int $category_id;
    private ?int $owner_id;
    private string $slug;
    private string $title;
    private string $descriptions;
    private float $price;
    private string $state;
    private string $product_image;
    private string $status;
    private string $creation_date;
    private string $modification_date;

    /**
     * Store the annonce table into an object
     * 
     * @param array $row receive the values inside the table
     * @return self
     */
    public static function fromArray(array $row): Annonce
    {
        $annonce = new self();
        $annonce->annonce_id = $row["annonce_id"] ?? null;
        $annonce->category_id = $row["category_id"] ?? null;
        $annonce->owner_id = $row["owner_id"] ?? null;
        $annonce->slug = $row["slug"] ?? "";
        $annonce->title = $row["title"] ?? "";
        $annonce->descriptions = $row["descriptions"] ?? "";
        $annonce->price = (float) ($row["price"] ?? 0);
        $annonce->state = $row["state"] ?? "";
        $annonce->product_image = $row["product_image"] ?? "";
        $annonce->status = $row["status"] ?? "active";
        $annonce->creation_date = $row["creation_date"] ?? "";
        $annonce->modification_date = $row["modification_date"] ?? "";

        return $annonce;
    }

    /**
     * Search an annonce by its ID
     * 
     * @param int $annonce_id
     * @return self|null according to the search result
     */
    public static function findById(int $annonce_id): ?self
    {
        $sql = "SELECT * FROM " . self::TABLE. " WHERE annonce_id = :id";
        $params = ["id" => $annonce_id];
        $result = Database::fetch($sql, $params);

        return $result ? self::fromArray($result) : null;
    }

    /**
     * Search all annonces by their category ID
     * 
     * @param int $category_id
     * @return array because it could fetch multiple rows from the database
     */
    public static function findByCategory(int $category_id): ?array
    {
        $sql = "SELECT * FROM " . self::TABLE. " WHERE category_id = :id";
        $params = ["id" => $category_id];
        $results = Database::fetchAll($sql, $params);

        $annonces = [];
        foreach ($results as $result) {
            $annonces[] = self::fromArray($result);
        }

        return ($annonces) ? $annonces : null;
    }

    /**
     * Search all annonces by Their owner ID
     * 
     * @param int $owner_id
     * @return array because it could fetch multiple rows from the database
     */
    public static function findByOwner(int $owner_id): ?array
    {
        $sql = "SELECT * FROM " . self::TABLE. " WHERE owner_id = :id ORDER BY creation_date DESC";
        $params = ["id" => $owner_id];
        $results = Database::fetchAll($sql, $params);

        $annonces = [];
        foreach ($results as $result) {
            $annonces[] = self::fromArray($result);
        }

        return ($annonces) ? $annonces : null;
    }
    
    /**
     * Search an annonce by its slug
     * 
     * @param string $slug
     * @return self|null according to the search result
     */
    public static function findBySlug(string $slug): ?self
    {
        $sql = "SELECT * FROM " . self::TABLE. " WHERE slug = :slug";
        $params = ["slug" => $slug];
        $result = Database::fetch($sql, $params);

        return $result ? self::fromArray($result) : null;
    }

    /**
     * Search all the annonces
     * 
     * @return array containing all the annonces
     */
    public static function findAll(): array
    {
        $sql = "SELECT * FROM " . self::TABLE. " ORDER BY creation_date DESC";
        $results = Database::fetchAll($sql);

        $annonces = [];
        foreach ($results as $result) {
            $annonces[] = self::fromArray($result);
        }

        return $annonces;
    }

    /**
     * Create a new annonce in the database
     * 
     * @throws Exception if validation failed
     * @return bool True on success
     */
    public function create(): bool
    {
        $validator = new Validator();
        $validator->validAnnonce("title", "descriptions", "price", [
            "title" => $this->title,
            "descriptions" => $this->descriptions,
            "price" => $this->price
        ]);

        if (!$validator->isValid()) {
            throw new Exception("VALIDATION ERROR : " . implode('; ', $validator->getErrors()));
        }

        $this->creation_date = date("Y-m-d H:i:s");
        $this->modification_date = date("Y-m-d H:i:s");

        $sql = "INSERT INTO " . self::TABLE . " 
        (category_id, owner_id, slug, title, descriptions, price, state, product_image, creation_date, modification_date, status) 
        VALUES 
        (:category_id, :owner_id,:slug, :title, :descriptions, :price, :state, :product_image, :creation_date, :modification_date, :status)
        ";
        
        $params = [
            "category_id" => $this->category_id,
            "owner_id" => $this->owner_id,
            "slug" => $this->slug,
            "title" => $this->title,
            "descriptions" => $this->descriptions,
            "price" => $this->price,
            "state" => $this->state,
            "product_image" => $this->product_image,
            "creation_date" => $this->creation_date,
            "modification_date" => $this->modification_date,
            "status" => $this->status
        ];

        Database::query($sql, $params);

        $this->annonce_id = (int)Database::lastInsertId();
        return true;
    }

    /**
     * Update an annonce informations in the database
     * 
     * @throws Exception if validation failed
     * @return bool True on success
     */
    public function update(): bool
    {
        $validator = new Validator();
        $validator->checkAnnonce("annonces", $this->annonce_id)
            ->validAnnonce("title", "descriptions", "price", [
            "title" => $this->title,
            "descriptions" => $this->descriptions,
            "price" => $this->price
        ]);

        if (!$validator->isValid()) {
            throw new Exception("VALIDATION ERROR : " . implode('; ', $validator->getErrors()));
        }

        $this->modification_date = date("Y-m-d H:i:s");

        $sql = "UPDATE " . self::TABLE . " SET
            category_id = :category_id,
            owner_id = :owner_id,
            slug = :slug,
            title = :title,
            descriptions = :descriptions,
            price = :price,
            state = :state,
            product_image = :product_image,
            modification_date = :modification_date,
            status = :status
            WHERE annonce_id = :id
        ";

        $params = [
            "id" => $this->annonce_id,
            "category_id" => $this->category_id,
            "owner_id" => $this->owner_id,
            "slug" => $this->slug,
            "title" => $this->title,
            "descriptions" => $this->descriptions,
            "price" => $this->price,
            "state" => $this->state,
            "product_image" => $this->product_image,
            "modification_date" => $this->modification_date,
            "status" => $this->status
        ];

        Database::query($sql, $params);

        return true;
    }

    /**
     * Delete an annonce in the database
     * 
     * @throws Exception if category doesn't exist
     * @return bool True on success
     */
    public function delete(): bool
    {
        $validator = new Validator();
        $validator->checkAnnonce("annonce", $this->annonce_id);
        
        if (!$validator->isValid()) {
            throw new Exception("FAIL to delete : annonces doesn't exist");
        }
        
        $sql = "DELETE FROM " . self::TABLE . " WHERE annonce_id = :id";
        $params = ["id" => $this->annonce_id];

        Database::query($sql, $params);
        return true;
    }

    
    /**
     * Set new annonce id to the annonce object
     * 
     * @return self the object
     */
    public function setAnnonceId(int $annonce_id): self 
    {   
        $this->annonce_id = $annonce_id;
        return $this;
    }
    
    /**
     * Set new category id to the annonce object
     * 
     * @return self the object
     */
    public function setCategoryId(int $category_id): self 
    {   
        $this->category_id = $category_id;
        return $this;
    }
    
    /**
     * Set new Onwner id to the annonce object
     * 
     * @return self the object
     */
    public function setOwnerId(int $owner_id): self 
    {   
        $this->owner_id = $owner_id;
        return $this;
    }
    
    /**
     * Set new slug to the annonce object
     * 
     * @return self the object
     */
    public function setSlug(string $slug): self 
    {   
        $this->slug = $slug;
        return $this;
    }
    
    /**
     * Set new title to the annonce object
     * 
     * @return self the object
     */
    public function setTitle(string $title): self 
    {   
        $this->title = $title;
        return $this;
    }
    
    /**
     * Set new description to the annonce object
     * 
     * @return self the object
     */
    public function setDescriptions(string $descriptions): self 
    {   
        $this->descriptions = $descriptions;
        return $this;
    }
    
    /**
     * Set new price to the annonce object
     * 
     * @return self the object
     */
    public function setPrice(float $price): self 
    {   
        $this->price = $price;
        return $this;
    }
    
    /**
     * Set new product state to the annonce object
     * 
     * @return self the object
     */
    public function setState(string $state): self 
    {   
        $this->state = $state;
        return $this;
    }
    
    /**
     * Set new product image to the annonce object
     * 
     * @return self the object
     */
    public function setProductImage(string $product_image): self 
    {   
        $this->product_image = $product_image;
        return $this;
    }
    
    /**
     * Set new availability status to the annonce object
     * 
     * @return self the object
     */
    public function setStatus(string $status): self 
    {   
        $this->status = $status;
        return $this;
    }

    /**
     * Get new annonce id to the annonce object
     * 
     * @return null|int the object
     */
    public function getAnnonceId(): ?int { return $this->annonce_id; }
    
    /**
     * Get new category id to the annonce object
     * 
     * @return null|int the object
     */
    public function getCategoryId(): ?int { return $this->category_id; }
    
    /**
     * Get new Onwner id to the annonce object
     * 
     * @return null|int the object
     */
    public function getOwnerId(): ?int { return $this->owner_id; }
    
    /**
     * Get new slug to the annonce object
     * 
     * @return string the object
     */
    public function getSlug(): string { return $this->slug; }
    
    /**
     * Get new title to the annonce object
     * 
     * @return string the object
     */
    public function getTitle(): string { return $this->title; }
    
    /**
     * Get new description to the annonce object
     * 
     * @return string the object
     */
    public function getDescriptions(): string { return $this->descriptions; }
    
    /**
     * Get new price to the annonce object
     * 
     * @return null|float the object
     */
    public function getPrice(): float { return $this->price; }
    
    /**
     * Get new product state to the annonce object
     * 
     * @return string the object
     */
    public function getState(): string { return $this->state; }
    
    /**
     * Get new product image to the annonce object
     * 
     * @return string the object
     */
    public function getProductImage(): string { return $this->product_image; }
    
    /**
     * Get new availability status to the annonce object
     * 
     * @return string the object
     */
    public function getStatus(): string { return $this->status; }
  
    
}