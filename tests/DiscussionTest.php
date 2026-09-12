<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "config/config.php";

use App\Core\Database;
use App\Models\{
    User, Category, Annonce, Discussion
};
use PHPUnit\Framework\TestCase;

final class DiscussionTest extends TestCase {

    private static User $Customer;
    private static User $newCustomer;
    private static User $Seller;
    private static User $newSeller;
    private static Category $Category;
    private static Annonce $Annonce;
    private static Discussion $Discussion;
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
        chargerEnv(NS_REP . '.env');

        $DB_HOST = $_ENV["DB_HOST"];
        $DB_NAME = $_ENV["DB_NAME"];
        $DB_USER = $_ENV["DB_USER"];
        $DB_PASS = $_ENV["DB_PASS"];

        Database::init($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS);
    }

    /**
     * set a test customer for every action that require an user
     */
    public function createTestCustomer(): User
    {
        $suffix = uniqid();
        $User = new User();
        $User->setFirstname("TESTE")
            ->setLastname("Customer")
            ->setEmail("testeCustomer_{$suffix}@gmail.com")
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
     * 
     */
    public function createTestNewCustomer()
    {
        $suffix = uniqid();
        $User = new User();
        $User->setFirstname("New")
            ->setLastname("TestCustomer")
            ->setEmail("testeNewCustomer_{$suffix}@gmail.com")
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
     * set a test seller for every action that require an user
     */
    public function createTestSeller(): User
    {
        $suffix = uniqid();
        $User = new User();
        $User->setFirstname("TesT")
            ->setLastname("Seller")
            ->setEmail("testeSeller_{$suffix}@gmail.com")
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
     * 
     */
    public function createTestNewSeller()
    {
        $suffix = uniqid();
        $User = new User();
        $User->setFirstname("NEWS")
            ->setLastname("TestSeller")
            ->setEmail("testeNewSeller_{$suffix}@gmail.com")
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
        $suffix = uniqid();
        $Category = new Category();
        $Category->setName("Testing Cat_{$suffix}")
            ->setSlug("testing-cat_{$suffix}")
            ->setDescriptions("A categoy testing data");
        $Category->create();

        return $Category;
    }

    /**
     * set an annonce for every action that require an annonce
     */
    public function createTestAnnonce(): Annonce
    {
        $Annonce = new Annonce();
        $Annonce->setCategoryId(self::$Category->getCategoryId())
            ->setOwnerId(self::$Seller->getUserId())
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
     * Create setup data for the discussion object
     */
    public function setTestDiscussion(): Discussion
    {
        $Discussion = new Discussion();
        $Discussion->setAnnonceId(self::$Annonce->getAnnonceId())
            ->setCustomerId(self::$Customer->getUserId())
            ->setSellerId(self::$Seller->getUserId())
            ->setStatus("active");
        return $Discussion;
    }

    protected function setUp(): void
    {
        self::$Customer = $this->createTestCustomer();
        self::$newCustomer = $this->createTestNewCustomer();
        self::$Seller = $this->createTestSeller();
        self::$newSeller = $this->createTestNewSeller();
        self::$Category = $this->createTestCategory();
        self::$Annonce = $this->createTestAnnonce();
        self::$Discussion = $this->setTestDiscussion();
        self::$Discussion->create();
    }

    /**
     * Delete all the setup data from the database
     */
    public function tearDown(): void
    {
        Database::query("DELETE FROM discussions");
        Database::query("DELETE FROM annonces WHERE slug LIKE :slug", ["slug" => "testing-%"]);
        Database::query("DELETE FROM categories WHERE slug LIKE :slug", ["slug" => "testing-%"]);
        Database::query("DELETE FROM users WHERE email LIKE :email", ["email" => "%@gmail.com"]);
    }

    /**
     * Test that a discussion can be found based on its identifier
     */
    public function testFindDiscussionByIdReturnsDiscussion()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $discussionId = self::$Discussion->getDiscussionId();
            $found = Discussion::findById($discussionId);

            self::assertInstanceOf(Discussion::class, $found);
            self::assertSame($discussionId, $found->getDiscussionId());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that a discussion can not be found with an non existing identifier
     */
    public function testFindDiscussionByIdReturnsNull()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $discussionId = -2;
            $found = Discussion::findById($discussionId);

            self::assertNull($found);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that a list of discussion is returned when the annonce exist
     */
    public function testFindDiscussionByAnnonceIdReturnsDiscussions()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $annonceId = self::$Discussion->getAnnonceId();
            $found = Discussion::findByAnnonceId($annonceId);

            self::assertIsArray($found);
            self::assertNotEmpty($found);
            self::assertSame($annonceId, $found[0]->getAnnonceId());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that an empty list of discussion is returned when the annonce doesn't exist
     */
    public function testFindDiscussionByAnnonceIdReturnsEmpty()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $annonceId = -2;
            $found = Discussion::findByAnnonceId($annonceId);

            self::assertIsArray($found);
            self::assertEmpty($found);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Test that a list of discussion is returned when the Customer id exist
     */
    public function testFindDiscussionByCustomerIdReturnsDiscussion()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $customerId = self::$Customer->getUserId();
            $found = Discussion::findByCustomerId($customerId);
            
            self::assertIsArray($found);
            self::assertNotEmpty($found);
            self::assertSame($customerId, $found[0]->getCustomerId());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that an empty list of discussion is return when the customer id doesn't exist
     */
    public function testFindDiscussionByCustomerIdReturnsEmpty()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $customerId = -2;
            $found = Discussion::findByCustomerId($customerId);

            self::assertIsArray($found);
            self::assertEmpty($found);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    /**
     * Test that a list of discussion is return when the seller id exist
     */
    public function testFindDiscussionBySellerIdReturnsDiscussion()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $sellerId = self::$Seller->getUserId();
            $found = Discussion::findBySellerId($sellerId);

            self::assertIsArray($found);
            self::assertNotEmpty($found);
            self::assertSame($sellerId, $found[0]->getSellerId());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that an empty list of list when the seller id doesn't exist
     */
    public function testFindDiscussionBySellerIdReturnsEmpty()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $sellerId = -2;
            $found = Discussion::findBySellerId($sellerId);

            self::assertIsArray($found);
            self::assertEmpty($found);
            
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that a list of all discussions is returned
     */
    public function testFindAllDiscussion()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $found = Discussion::findAll();

            self::assertIsArray($found);
            self::assertNotEmpty($found);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that no discussion is returned
     */
    public function testFindNoDiscussion()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            Database::query("DELETE FROM discussions");
            $found = Discussion::findAll();

            self::assertIsArray($found);
            self::assertEmpty($found);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test create a discussion without required informations
     */
    public function testCreateInvalidDiscussion()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }
                
            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");
            
            $invalid = new Discussion();
            $invalid->create();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test create a discussion without an annonce identifier
     */
    public function testCreateDiscussionWithoutAnnonceId()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }
                
            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");
            
            $invalid = new Discussion();
            $invalid->setCustomerId(self::$Customer->getUserId())
                ->setSellerId(self::$Seller->getUserId())
                ->setStatus("active")
                ->create();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test create a discussion without a customer identifier
     */
    public function testCreateDiscussionWithoutCustomerId()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }
                
            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");
            
            $invalid = new Discussion();
            $invalid->setAnnonceId(self::$Annonce->getAnnonceId())
                ->setSellerId(self::$Seller->getUserId())
                ->setStatus("active")
                ->create();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test create a discussion without a seller identifier
     */
    public function testCreateDiscussionWithoutSellerId()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }
                
            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");
            
            $invalid = new Discussion();
            $invalid->setAnnonceId(self::$Annonce->getAnnonceId())
                ->setCustomerId(self::$Customer->getUserId())
                ->setStatus("active")
                ->create();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test create a discussion with an invalid status
     */
    public function testCreateDiscussionWithInvalidStatus()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }
                
            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");
            
            $invalid = new Discussion();
            $invalid->setAnnonceId(self::$Annonce->getAnnonceId())
                ->setCustomerId(self::$Customer->getUserId())
                ->setSellerId(self::$Seller->getUserId())
                ->setStatus("invalid")
                ->create();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test create a discussion with identical customer and seller identifier
     */
    public function testCreateDiscussionWithEqualCustomerAndSellerId()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }
                
            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");
            
            $invalid = new Discussion();
            $invalid->setAnnonceId(self::$Annonce->getAnnonceId())
                ->setCustomerId(self::$Customer->getUserId())
                ->setSellerId(self::$Customer->getUserId())
                ->setStatus("active")
                ->create();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test update of a discussion with valid informations
     */
    public function testUpdateValidDiscussion()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $update = self::$Discussion->setCustomerId(self::$newCustomer->getUserId())
                ->setSellerId(self::$newSeller->getUserId())
                ->setStatus("closed");
            $update->update();

            $found = Discussion::findById(self::$Discussion->getDiscussionId());
            
            self::assertInstanceOf(Discussion::class, $update);
            self::assertSame($update->getDiscussionId(), $found->getDiscussionId());
            self::assertSame(self::$newCustomer->getUserId(), $found->getCustomerId());
            self::assertSame(self::$newSeller->getUserId(), $found->getSellerId());
            self::assertSame("closed", $found->getStatus());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Test update of a discussion with invalid informations
     */
    public function testUpdateInvalidDiscussion()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $update = self::$Discussion->setCustomerId(2)
                ->setSellerId(2)->setStatus("");
            
            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");
            $update->update();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test deleting an existing discussion
     */
    public function testDeleteExistingDiscussion()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }
            
            self::assertTrue(self::$Discussion->delete());
            self::assertNull(Discussion::findById(self::$Discussion->getDiscussionId()));

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test deleting a non existing discussion
     */
    public function testDeleteNonExistingDiscussion()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/DELETION FAIL/");
            self::$Discussion->setDiscussionId(-2)->delete();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}