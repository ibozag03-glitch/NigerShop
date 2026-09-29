<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "config/config.php";

use PHPUnit\Framework\TestCase;
use App\Core\Database;
use App\Models\User;
use App\Models\Category;
use App\Models\Annonce;
use App\Models\Favorites;

final class FavoritesTest extends TestCase {

    private static User $User;
    private static Category $Category;
    private static Annonce $Annonce;
    private static Favorites $Favorites;
    private static array $annonce = [
        "slug" => "testing-favorite-annonce-slug",
        "title" => "Testing Favorite Annonce Title",
        "descriptions" => "Testing favorite annonce description, must be at least 20 characters long.",
        "price" => 1500.0,
        "state" => "new",
        "product_image" => "testing-image.jpg",
        "status" => "active"
    ];

    /**
     * Set up before all tests - initialize database once
     */
    public static function setUpBeforeClass(): void
    {
        chargerEnv(NS_REP . ".env");
        
        $DB_HOST = $_ENV["DB_HOST"];
        $DB_NAME = $_ENV["DB_NAME"];
        $DB_USER = $_ENV["DB_USER"];
        $DB_PASS = $_ENV["DB_PASS"];

        Database::init($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS);
    }

    /**
     * set a test user for every action that require an user
     */
    public function createTestUser(): User
    {
        $User = new User();
        $User->setFirstname("Test")
            ->setLastname("User")
            ->setEmail("testeUser@gmail.com")
            ->setPassword("TestUserpassword.01")
            ->setAddress("Somewhere in the code")
            ->setPhone("+227 33 44 55 66")
            ->setBirthDate("20-07-2007")
            ->setRole()
            ->setStatus();
        $User->create();

        return $User;
    }

    /**
     * set a test category for every action that require a category
     */
    public function createTestCategory(): Category
    {
        $Category = new Category();
        $Category->setName("Testing Favorite Category")
            ->setSlug("testing-favorite-category")
            ->setDescriptions("A category testing favorite data");
        $Category->create();

        return $Category;
    }

    /**
     * set a test annonce for every action that require a annonce
     */
    public function createTestAnnonce(): Annonce
    {
        $Annonce = new Annonce();
        $Annonce->setCategoryId(self::$Category->getCategoryId())
            ->setOwnerId(self::$User->getUserId())
            ->setSlug(self::$annonce['slug'])
            ->setTitle(self::$annonce['title'])
            ->setDescriptions(self::$annonce['descriptions'])
            ->setPrice(self::$annonce['price'])
            ->setState(self::$annonce['state'])
            ->setProductImage(self::$annonce['product_image'])
            ->setStatus(self::$annonce['status']);
        $Annonce->create();

        return $Annonce;
    }

    /**
     * create setup data in the Favorite object to insert in the database
     */
    public function setFavorite(): Favorites
    {
        $Favorites = new Favorites();
        $Favorites->setAnnonceId(self::$Annonce->getAnnonceId())
            ->setUserId(self::$User->getUserId());
        return $Favorites;
    }

    /**
     * Delete all the setup data from the database
     */
    public function tearDown(): void
    {
        Database::query("DELETE FROM favorites");
        Database::query("DELETE FROM annonces WHERE slug LIKE :slug", ["slug" => "testing-%"]);
        Database::query("DELETE FROM categories WHERE slug LIKE :slug", ["slug" => "testing-%"]);
        Database::query("DELETE FROM users WHERE email LIKE :email", ["email" => "%@gmail.com"]);
    }

    /**
     * Test that favorites can be retrieved by user ID when they exist.
     */
    public function testFindFavoritesByUserRetrunsFavorites()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->createTestAnnonce();
                self::$Favorites = $this->setFavorite();
                self::$Favorites->add();
                
                $found = Favorites::findByUser(self::$User->getUserId());

                self::assertIsArray($found);
                self::assertNotEmpty($found);
                self::assertInstanceOf(Favorites::class, $found[0]);
                self::assertSame($found[0]->getAnnonceId(), self::$Annonce->getAnnonceId());
                self::assertSame($found[0]->getUserId(), self::$User->getUserId());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test that an empty list is returned when a user has no favorites.
     */
    public function testFindFavoritesByUserReturnsEmpty()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                
                $notFound = Favorites::findByUser(self::$User->getUserId());

                self::assertIsArray($notFound);
                self::assertEmpty($notFound);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }
    
    /**
     * Test that favorites can be retrieved by annonce ID when they exist.
     */
    public function testFindFavoritesByAnnonceReturnsFavorite()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->createTestAnnonce();
                self::$Favorites = $this->setFavorite();
                self::$Favorites->add();
                
                $found = Favorites::findByAnnonce(self::$Annonce->getAnnonceId());

                self::assertIsArray($found);
                self::assertNotEmpty($found);
                self::assertInstanceOf(Favorites::class, $found[0]);
                self::assertSame($found[0]->getAnnonceId(), self::$Annonce->getAnnonceId());
                self::assertSame($found[0]->getUserId(), self::$User->getUserId());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test that an empty list is returned when an annonce has no favorites.
     */
    public function testFindFavoritesByAnnonceReturnsEmpty()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->createTestAnnonce();
                
                $notFound = Favorites::findByAnnonce(self::$Annonce->getAnnonceId());

                self::assertIsArray($notFound);
                self::assertEmpty($notFound);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test that a favorite record is found when both the user and annonce IDs match.
     */
    public function testFindFavoritesByUserAndAnnonceReturnsFavorite()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->createTestAnnonce();
                self::$Favorites = $this->setFavorite();
                self::$Favorites->add();
                
                $found = Favorites::findByUserAndAnnonce(
                    self::$User->getUserId(), 
                    self::$Annonce->getAnnonceId()
                );

                self::assertInstanceOf(Favorites::class, $found);
                self::assertNotEmpty($found);
                self::assertSame($found->getAnnonceId(), self::$Annonce->getAnnonceId());
                self::assertSame($found->getUserId(), self::$User->getUserId());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test that no favorite is returned when the user/annonce pair does not exist.
     */
    public function testFindFavoritesByUserAndAnnonceReturnsNull()
    {
         try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->createTestAnnonce();
                
                $notFoundedFavorites = Favorites::findByUserAndAnnonce(
                    self::$User->getUserId(), 
                    self::$Annonce->getAnnonceId()
                );

                self::assertNull($notFoundedFavorites);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test that all favorites can be retrieved from the database.
     */
    public function testFindAllFavorites()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->createTestAnnonce();
                self::$Favorites = $this->setFavorite();
                self::$Favorites->add();
                
                $found = Favorites::findAll();

                self::assertIsArray($found);
                self::assertNotEmpty($found);
                self::assertInstanceOf(Favorites::class, $found[0]);
                self::assertSame($found[0]->getAnnonceId(), self::$Annonce->getAnnonceId());
                self::assertSame($found[0]->getUserId(), self::$User->getUserId());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test that the full favorites list is empty when no favorites exist.
     */
    public function testFindNoFavorites()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->createTestAnnonce();
                
                $notFoundedFavorites = Favorites::findAll();

                self::assertIsArray($notFoundedFavorites);
                self::assertEmpty($notFoundedFavorites);               
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test that the number of favorites for a user is counted correctly.
     */
    public function testCountByUserFavorites()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->createTestAnnonce();
                self::$Favorites = $this->setFavorite();
                self::$Favorites->add();
                
                $count = Favorites::countByUser(self::$User->getUserId());

                self::assertIsInt($count);
                self::assertSame(1, $count);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test that a favorite can be added successfully with valid user and annonce IDs.
     */
    public function testAddValidFavorite()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->createTestAnnonce();
                self::$Favorites = $this->setFavorite();
                $valid = self::$Favorites->add();

                self::assertTrue($valid);

                $found = Favorites::findByUserAndAnnonce(
                    self::$User->getUserId(), 
                    self::$Annonce->getAnnonceId()
                );

                self::assertInstanceOf(Favorites::class, $found);
                self::assertNotNull($found);
                self::assertSame($found->getAnnonceId(), self::$Annonce->getAnnonceId());
                self::assertSame($found->getUserId(), self::$User->getUserId());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test that a duplicate favorite triggers an exception.
     */
    public function testAddInvalidFavorite()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->createTestAnnonce();
                self::$Favorites = $this->setFavorite();
                self::$Favorites->add();

                self::expectException(Exception::class);
                self::expectExceptionMessageMatches('/FAIL TO ADD/');
                self::$Favorites->add();
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test that an existing favorite can be removed successfully.
     */
    public function testRemoveExistingFavorite()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->createTestAnnonce();
                self::$Favorites = $this->setFavorite();
                self::$Favorites->add();

                self::assertTrue(self::$Favorites->remove());

                $found = Favorites::findByUserAndAnnonce(
                    self::$User->getUserId(), 
                    self::$Annonce->getAnnonceId()
                );

                self::assertNull($found);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }
    
}