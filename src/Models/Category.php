<?php
/**
 * 
*/
namespace App\Models;

use App\Core\Database;
use App\Core\Validator;
use Exception;

class Category {
    
    private const TABLE = "categories"; 
    private ?int $category_id;
    private string $slug;
    private string $name;
    private string $descriptions;

    /**
     * Store the category table into an object
     * 
     * @param array $row receive the values inside the table
     * @return self
     */
    public static function fromArray(array $row): self
    {
        $category = new self();
        $category->category_id = $row["category_id"] ?? null;
        $category->slug = $row["slug"] ?? "";
        $category->name = $row["name"] ?? "";
        $category->descriptions = $row["descriptions"] ?? "";

        return $category;
    }

    /**
     * Search a category by its ID
     * 
     * @param int $category_id
     * @return self|null according to the search result
     */
    public static function findById(int $category_id): ?self
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE category_id = :id";
        $param = ["id" => $category_id];
        $result = Database::fetch($sql, $param);

        return $result ? self::fromArray($result) : null;
    }

    /**
     * Search the category by its slug
     * 
     * @param string $slug
     * @return self|null according to the search result
     */
    public static function findBySlug(string $slug): ?self
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE slug = :slug";
        $param = ["slug" => $slug];
        $result = Database::fetch($sql, $param);

        return $result ? self::fromArray($result) : null;
    }

    /**
     * Search the category by its name
     * 
     * @param string $name
     * @return self|null according to the search result
     */
    public static function findByName(string $name): ?self
    {    
        $sql = "SELECT * FROM " . self::TABLE . " WHERE name = :name";
        $param = ["name" => $name];
        $result = Database::fetch($sql, $param);

        return $result ? self::fromArray($result) : null;
    }

    /**
     * Search all the categories
     * 
     * @return array containing all the categories
     */
    public static function findAll(): array
    {        
        $sql = "SELECT * FROM " . self::TABLE . " ORDER BY name ASC";
        $results = Database::fetchAll($sql);

        $categories = [];
        foreach ($results as $result) {
            $categories[] = self::fromArray($result);
        }

        return $categories;
    }

    /**
     * Create new category in the database
     * 
     * @throws Exception if validation failed
     * @return bool True on success
     */
    public function create(): bool
    {
        $validator = new Validator();        
        $validator->validCategory("name", "slug", "descriptions", [
            "name" => $this->name,
            "slug" => $this->slug,
            "descriptions" => $this->descriptions
        ]);

        if (!$validator->isValid()) {
            throw new Exception("VALIDATION ERROR : " . implode('; ', $validator->getErrors()));
        }

        $sql = "INSERT INTO " . self::TABLE . " (name, slug, descriptions) VALUES (:name, :slug, :descriptions)";
        $params = [
            "name" => $this->name,
            "slug" => $this->slug,
            "descriptions" => $this->descriptions
        ];

        Database::query($sql, $params);

        $this->category_id = (int)Database::lastInsertId();

        return true;
    }

    /**
     * Update a category informations in the database
     * 
     * @throws Exception if validation failed
     * @return bool True on success
     */
    public function update(): bool
    {
        $validator = new Validator();
        $validator->checkCategory("category", $this->category_id);

        if (!$validator->isValid()) {
            throw new Exception("FAIL to update : category doesn't exist.");
        }
        
        $validator->validCategory("name", "slug", "descriptions", [
            "name" => $this->name,
            "slug" => $this->slug,
            "descriptions" => $this->descriptions
        ]);
            
        if (!$validator->isValid()) {
            throw new Exception("VALIDATION ERROR : " . implode('; ', $validator->getErrors()));
        }

        $sql = "UPDATE " . self::TABLE . " SET name = :name, slug = :slug, descriptions = :descriptions WHERE category_id = :category_id";
        $params = [
            "category_id" => $this->category_id,
            "name" => $this->name,
            "slug" => $this->slug,
            "descriptions" => $this->descriptions
        ];

        Database::query($sql, $params);

        return true;
    }

    /**
     * Delete the category in the database
     * 
     * @throws Exception if category doesn't exist
     * @return bool True on success
     */
    public function delete(): bool
    {
        $validator = new Validator();
        $validator->checkCategory("category", $this->category_id);
        
        if (!$validator->isValid()) {
            throw new Exception("FAIL to delete : category doesn't exist");
        }
        
        $sql = "DELETE FROM " . self::TABLE . " WHERE category_id = :id";
        $params = ["id" => $this->category_id];

        Database::query($sql, $params);

        return true;
    }

    /**
     * Set new category id to the category object
     * 
     * @return self the object
     */
    public function setCategoryId(int $category_id): self
    {
        $this->category_id = $category_id;
        return $this;
    }

    /**
     * Set new category name to the category object
     * 
     * @return self the object
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * set new category slug to the category object
     * 
     * @return self the object
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * set new description to the category object
     * 
     * @return self the object
     */
    public function setDescriptions(string $descriptions): self
    {
        $this->descriptions = $descriptions;
        return $this;
    }

    /**
     * get the category id from the category object
     * 
     * @return int|null the object
     */
    public function getCategoryId(): ?int { return $this->category_id; }

    /**
     * get the category name from the category object
     * 
     * @return string|null the object
     */
    public function getName(): ?string { return $this->name; }

    /**
     * get the category slug from the category object
     * 
     * @return string|null the object
     */
    public function getSlug(): ?string { return $this->slug; }

    /**
     * get the category description from the category object
     * 
     * @return string|null the object
     */
    public function getDescriptions(): ?string { return $this->descriptions; }

}