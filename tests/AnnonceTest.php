<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "config/config.php";

use App\Core\Database;
use App\Models\Annonce;
use App\Models\User;
use App\Models\Category;
use PHPUnit\Framework\TestCase;

final class AnnonceTest extends TestCase {

    private static User $User;
    private static Category $Category;
    private static Annonce $Annonce;
    private static array $annonce = [
        "slug" => "testing-annonce-slug",
        "title" => "Testing Annonce Title",
        "descriptions" => "Testing annonce description, must be at least 20 characters long.",
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
        chargerEnv(NS_REP . '.env');

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
            ->setBirthDate("20/07/2007")
            ->setRole()
            ->setStatus();
        $User->create();

        return $User;
    }

    /**
     * set a category for every action that require a category
     */
    public function createTestCategory(): Category
    {
        $Category = new Category();
        $Category->setName("Testing Category")
            ->setSlug("testing-category")
            ->setDescriptions("A categoy testing data");
        $Category->create();

        return $Category;
    }

    /**
     * create setup data in the Annonce object to insert in the database
     */
    public function setAnnonce(): Annonce
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
        return $Annonce;
    }

    /**
     * Delete all the setup data from the database
     */
    public function tearDown(): void
    {
        Database::query("DELETE FROM annonces WHERE slug LIKE :slug", ["slug" => "testing-%"]);
        Database::query("DELETE FROM categories WHERE slug LIKE :slug", ["slug" => "testing-%"]);
        Database::query("DELETE FROM users WHERE email LIKE :email", ["email" => "%@gmail.com"]);
    }

    /**
     * Test if the annonce exist when it is setted
     */
    public function testFindAnnonceByIdReturnsAnnonce()
    {
        try {
            if (Database::isConnected()) {
                //Create an user, a category and set an annonce
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->create();

                //get the annonce ID and search if it really exist
                $annonceId = self::$Annonce->getAnnonceId();
                $foundedAnnonce = self::$Annonce::findById($annonceId);

                //Make assertions to test return of the Annonce model method and check the ID correspondance 
                self::assertInstanceOf(Annonce::class, $foundedAnnonce);
                self::assertSame($annonceId, $foundedAnnonce->getAnnonceId());
                self::assertSame(self::$annonce['title'], $foundedAnnonce->getTitle());
                self::assertSame(self::$annonce['slug'], $foundedAnnonce->getSlug());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if the annonce exist when there is no annonce set
     */
    public function testFindAnnonceByIdReturnsNull()
    {
        try {
            if (Database::isConnected()) {
                $notFoundedAnnonce = Annonce::findById(1);
                self::assertNull($notFoundedAnnonce);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test if the annonce does exist with an existent Category ID
     */
    public function testFindAnnonceByCategoryReturnsAnnonce()
    {
        try {
            if (Database::isConnected()) {
                //Create an user, a category and set an annonce
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->create();

                //get the category ID and search if it really exist
                $categoryId = self::$Category->getCategoryId();
                $foundedAnnonceByCategory = self::$Annonce::findByCategory($categoryId);

                //Make assertions to test the return of the Annonce model method and check the ID correspondance
                self::assertIsArray($foundedAnnonceByCategory);
                self::assertInstanceOf(Annonce::class, $foundedAnnonceByCategory[0]);
                self::assertSame($categoryId, $foundedAnnonceByCategory[0]->getCategoryId());
                self::assertSame(self::$annonce['title'], $foundedAnnonceByCategory[0]->getTitle());
                self::assertSame(self::$annonce['slug'], $foundedAnnonceByCategory[0]->getSlug());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test if the annonce doesn't exist with an unexistent Category ID
     */
    public function testFindAnnonceByCategoryReturnsNull()
    {
        try {
            if (Database::isConnected()) {
                //Create an user, a category and set an annonce
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->create();

                //get the category ID and search if it really exist
                $categoryId = 9999;
                $notFoundedAnnonceByCategory = self::$Annonce::findByCategory($categoryId);

                //Make assertions to test that the return of the Annonce model method is null or empty
                self::assertNull($notFoundedAnnonceByCategory);
                self::assertEmpty($notFoundedAnnonceByCategory);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test if the annonce does exist with an unexistent owner(user) ID
     */
    public function testFindAnnonceByOwnerReturnsAnnonce()
    {
        try {
            if (Database::isConnected()) {
                //Create an user, a category and set an annonce
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->create();

                //get the user ID and search if it really exist
                $userId = self::$User->getUserId();
                $foundedAnnonceByOwner = self::$Annonce::findByOwner($userId);

                //Make assertions to test the return of the Annonce model method and check the ID correspondance
                self::assertIsArray($foundedAnnonceByOwner);
                self::assertInstanceOf(Annonce::class, $foundedAnnonceByOwner[0]);
                self::assertSame($userId, $foundedAnnonceByOwner[0]->getOwnerId());
                self::assertSame(self::$annonce['title'], $foundedAnnonceByOwner[0]->getTitle());
                self::assertSame(self::$annonce['slug'], $foundedAnnonceByOwner[0]->getSlug());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test if the annonce doesn't exist with an unexistent Category ID
     */
    public function testFindAnnonceByOwnerReturnsNull()
    {
        try {
            if (Database::isConnected()) {
                //Create an user, a category and set an annonce
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->create();

                //get the owner ID and search if it really exist
                $ownerId = 9999;
                $notFoundedAnnonceByOwner = self::$Annonce::findByOwner($ownerId);

                //Make assertions to test that the return of the Annonce model method is null or empty
                self::assertNull($notFoundedAnnonceByOwner);
                self::assertEmpty($notFoundedAnnonceByOwner);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test if the annonce can be find according to its slug
     */
    public function testFindAnnonceBySlugReturnsAnnonce()
    {
        try {
            if (Database::isConnected()) {
                //Create an user, a category and set an annonce
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->create();

                //get the annonce slug and search if it really exist
                $slug = self::$Annonce->getSlug();
                $foundedAnnonceBySlug = self::$Annonce::findBySlug($slug);

                //Make assertions to test the return of the Annonce model method and check the slug correspondance 
                self::assertInstanceOf(Annonce::class, $foundedAnnonceBySlug);
                self::assertSame($slug, $foundedAnnonceBySlug->getSlug());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test if the annonce will not be found with an unexistent slug
     */
    public function testFindAnnonceBySlugReturnsNull()
    {
        try {
            if (Database::isConnected()) {
                //Create an user, a category and set an annonce
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->create();

                //get the annonce slug and check if it can found a related annonce
                $slug = '';
                $notFoundedAnnonceBySlug = self::$Annonce::findBySlug($slug);

                //Make assertions to test that the return of the Annonce model method is null or empty
                self::assertNull($notFoundedAnnonceBySlug);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test if all annonces can be found
     */
    public function testFindAllAnnonces()
    {
        try {
            if (Database::isConnected()) {
                //Create an user, a category and set an annonce
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->create();

                //get All the annonces
                $foundedAnnonces = self::$Annonce::findAll();

                //Make assertions to test if the the model method return array and is not empty
                self::assertIsArray($foundedAnnonces);
                self::assertNotEmpty($foundedAnnonces);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }
    
    /**
     * Test if we can found no annonces
     */
    public function testFindNoAnnonces()
    {
        try {
            if (Database::isConnected()) {
                //Create an user, a category and set an annonce
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->create();

                //Delete the annonces and try get them All
                Database::query("DELETE FROM annonces");
                $notFoundedAnnonces = self::$Annonce::findAll();

                //Make assertions to test if the the model method return an empty array
                self::assertEmpty($notFoundedAnnonces);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test creation of an annonce with Valid informations
     */
    public function testCreateValidAnnonce()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();

                self::assertTrue(self::$Annonce->create());
                self::assertSame(self::$annonce['title'], self::$Annonce->getTitle());
                self::assertSame(self::$annonce['slug'], self::$Annonce->getSlug());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test creation of an annonce with invalid/unexistent information
     */
    public function testCreateInvalidAnnonce()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = new Annonce();

                $invalidAnnonce = self::$Annonce->setTitle("")
                    ->setDescriptions("")
                    ->setPrice(1.1);

                self::expectException(Exception::class);
                self::expectExceptionMessageMatches('/VALIDATION ERROR/');
                $invalidAnnonce->create();
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test update an annonce with vaild informations
     */
    public function testUpdateValidAnnonce()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->create();

                $validAnnonce = self::$Annonce->setSlug("testing-update-slug")
                    ->setTitle("Testing Update")
                    ->setDescriptions("this is a test for updating an existent annonce with valid data")
                    ->setPrice(1.1);
                $valid = $validAnnonce->update();
                
                self::assertTrue($valid);

                $updatedAnnonce = Annonce::findById(self::$Annonce->getAnnonceId());
                
                self::assertInstanceOf(Annonce::class, $updatedAnnonce);
                self::assertSame("testing-update-slug", $updatedAnnonce->getSlug());
                self::assertSame('Testing Update', $updatedAnnonce->getTitle());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }    
    }

    /**
     * Test update an annonce with invalid information
     */
    public function testUpdateInvalidAnnonce()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->create();

                $invalidAnnonce = self::$Annonce->setSlug("")
                    ->setTitle("")
                    ->setDescriptions("this is a test for updating an existent annonce with invalid data")
                    ->setPrice(1.1);

                self::expectException(Exception::class);
                self::expectExceptionMessageMatches('/VALIDATION ERROR/');
                $invalidAnnonce->update();
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * Test deleting an annonce when it exist
     */
    public function testDeleteExistingAnnonce()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->create();
                
                self::assertTrue(self::$Annonce->delete());
                self::assertNull(Annonce::findById(self::$Annonce->getAnnonceId()));
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }
    
    /**
     * Test deleting an annonce that doesn't unexist
     */
    public function testDeleteNonExistingAnnonce()
    {
        try {
            if (Database::isConnected()) {
                self::$User = $this->createTestUser();
                self::$Category = $this->createTestCategory();
                self::$Annonce = $this->setAnnonce();
                self::$Annonce->setAnnonceId(9999);

                self::expectException(Exception::class);
                self::expectExceptionMessageMatches('/FAIL to delete/');
                self::$Annonce->delete();
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }
}