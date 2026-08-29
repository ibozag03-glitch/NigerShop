<?php
/**
 * This files handle favorite annonces data operations and validations
*/
namespace App\Models;

use App\Core\Database;
use Exception;

class Favorites {

    private const TABLE = "favorites";
    private ?int $annonce_id;
    private ?int $user_id;
    private string $add_date;

    /**
     * Build a Favorites object from a database row.
     *
     * @param array $row Raw data returned by the favorites table
     * @return self A Favorites instance populated from the row
     */
    public static function fromArray(array $row): Favorites
    {
        $favorite = new self();
        $favorite->annonce_id = (int)$row['annonce_id'] ?? null;
        $favorite->user_id = (int)$row['user_id'] ?? null;
        $favorite->add_date = $row['add_date'] ?? "";

        return $favorite;
    }

    /**
     * Get all favorites for a specific user.
     *
     * @param int $user_id User identifier
     * @return array<int, self> List of favorite entries for the user
     */
    public static function findByUser(int $user_id): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE user_id = :id";
        $params = [
            "id" => $user_id
        ];
        $results = Database::fetchAll($sql, $params);

        $favorites = [];
        foreach ($results as $result) {
            $favorites[] = self::fromArray($result);
        }

        return $favorites;
    }

    /**
     * Get all users who saved a specific annonce as favorite.
     *
     * @param int $annonce_id Annonce identifier
     * @return array<int, self> List of favorite entries for the annonce
     */
    public static function findByAnnonce(int $annonce_id): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE annonce_id = :id";
        $params = [
            "id" => $annonce_id
        ];
        $results = Database::fetchAll($sql, $params);

        $favorites = [];
        foreach ($results as $result) {
            $favorites[] = self::fromArray($result);
        }

        return $favorites;
    }

    /**
     * Check whether a user has marked a specific annonce as favorite.
     *
     * @param int $user_id User identifier
     * @param int $annonce_id Annonce identifier
     * @return self|null The favorite record if it exists, otherwise null
     */
    public static function findByUserAndAnnonce(int $user_id, int $annonce_id): ?self
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE annonce_id = :annonce_id AND user_id = :user_id";
        $params = [
            "annonce_id" => $annonce_id,
            "user_id" => $user_id
        ];
        $result = Database::fetch($sql, $params);
        
        return $result ? self::fromArray($result) : null;
    }

    /**
     * Retrieve every favorite record from the table.
     *
     * @return array<int, self> Full list of favorites ordered by date descending
     */
    public static function findAll(): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " ORDER BY add_date DESC";
        
        $results = Database::fetchAll($sql);

        $favorites = [];
        foreach ($results as $result) {
            $favorites[] = self::fromArray($result);
        }

        return $favorites;
    }

    /**
     * Count how many annonces a user has saved as favorites.
     *
     * @param int $user_id User identifier
     * @return int Number of favorite annonces
     */
    public static function countByUser(int $user_id): int
    {
        $sql = "SELECT COUNT(*) AS total FROM " . self::TABLE . " WHERE user_id = :id";
        $params = [
            "id" => $user_id
        ];
        $result = Database::fetch($sql, $params);

        return (int)($result['total'] ?? 0);
    }

    /**
     * Add the current annonce to the user's favorites.
     *
     * @throws Exception If annonce_id or user_id is missing
     * @return bool True on success
     */
    public function add(): bool
    {
        if ($this->annonce_id === null || $this->user_id === null) {
            throw new Exception("FAIL TO ADD: Impossible d'ajouter une annonce aux favoris sans d'identifiant d'utilisateur et d'annonce");
        }
        
        $existingFavorite = Favorites::findByUserAndAnnonce($this->user_id, $this->annonce_id);
        if ($existingFavorite) {
            throw new Exception("FAIL TO ADD: This favorite already exist");
        }

        $this->add_date = date("Y-m-d H:i:s");

        $sql = "INSERT INTO " . self::TABLE . " (annonce_id, user_id, add_date) 
            VALUES (:annonce_id, :user_id, :add_date)";
        $params = [
            "annonce_id" => $this->annonce_id,
            "user_id" => $this->user_id,
            "add_date" => $this->add_date
        ];
        Database::query($sql, $params);

        return true;
    }

    /**
     * Remove the current annonce from the user's favorites.
     *
     * @throws Exception If annonce_id or user_id is missing
     * @return bool True on success
     */
    public function remove(): bool
    {
        if ($this->annonce_id === null || $this->user_id === null) {
            throw new Exception("Impossible de supprimer une annonce des favoris sans identifiant d'utilisateur et d'annonce");
        }

        $sql = "DELETE FROM " . self::TABLE . " WHERE annonce_id = :annonce_id AND user_id = :user_id";
        $params = [
            "annonce_id" => $this->annonce_id,
            "user_id" => $this->user_id
        ];
        Database::query($sql, $params);
        
        return true;
    }

    /**
     * Set the annonce identifier for this favorite entry.
     *
     * @param int $annonce_id Annonce identifier
     * @return self The current instance
     */
    public function setAnnonceId(int $annonce_id): self
    {
        $this->annonce_id = $annonce_id;
        return $this;
    }

    /**
     * Set the user identifier for this favorite entry.
     *
     * @param int $user_id User identifier
     * @return self The current instance
     */
    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * Get the annonce identifier.
     *
     * @return int|null The annonce ID or null if not set
     */
    public function getAnnonceId(): ?int { return $this->annonce_id; }

    /**
     * Get the user identifier.
     *
     * @return int|null The user ID or null if not set
     */
    public function getUserId(): ?int { return $this->user_id; }

    /**
     * Get the date when the favorite was added.
     *
     * @return string Favorite creation date in database format
     */
    public function getAddDate(): string { return $this->add_date; }
    
}