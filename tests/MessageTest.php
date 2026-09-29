<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "config/config.php";

use App\Core\Database;
use App\Models\{
    User, Category, Annonce, Discussion, Message
};
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase {

    private static User $Customer;
    private static User $Seller;
    private static Category $Category;
    private static Annonce $Annonce;
    private static Discussion $Discussion;
    private static Message $Message;
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
     * set a discussion for every action that require an discussion
     */
    public function createTestDiscussion(): Discussion
    {
        $Discussion = new Discussion();
        $Discussion->setAnnonceId(self::$Annonce->getAnnonceId())
            ->setCustomerId(self::$Customer->getUserId())
            ->setSellerId(self::$Seller->getUserId())
            ->setStatus("active");
        $Discussion->create();

        return $Discussion;
    }

    /**
     * Create setup data for the message object
     */
    public function setTestMessage(): Message
    {
        $suffix = uniqid();
        $Message = new Message;
        $Message->setContent("This is a test message with uniqid n° : {$suffix}")
            ->setReadState(FALSE)
            ->setDiscussionId(self::$Discussion->getDiscussionId())
            ->setSender(self::$Customer->getUserId())
            ->setReceiver(self::$Seller->getUserId());
        return $Message;
    }

    protected function setUp(): void
    {
        self::$Customer = $this->createTestCustomer();
        self::$Seller = $this->createTestSeller();
        self::$Category = $this->createTestCategory();
        self::$Annonce = $this->createTestAnnonce();
        self::$Discussion = $this->createTestDiscussion();
        self::$Message = $this->setTestMessage();
        self::$Message->create();
    }

    /**
     * Delete all the setup data from the database
     */
    public function tearDown(): void
    {
        Database::query("DELETE FROM messages");
        Database::query("DELETE FROM discussions WHERE discussion_id = :id", [
            "id" => self::$Discussion->getDiscussionId()
        ]);
        Database::query("DELETE FROM annonces WHERE slug LIKE :slug", ["slug" => "testing-%"]);
        Database::query("DELETE FROM categories WHERE slug LIKE :slug", ["slug" => "testing-%"]);
        Database::query("DELETE FROM users WHERE email LIKE :email", ["email" => "%@gmail.com"]);
    }

    /**
     * Test that a message can be found by its ID
     */
    public function testFindByIdReturnMessage()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $messageId = self::$Message->getMessageId();
            $found = Message::findById($messageId);

            self::assertInstanceOf(Message::class, $found);
            self::assertNotNull($found);
            self::assertSame($messageId, $found->getMessageId());
            self::assertSame(self::$Message->getContent(), $found->getContent());
            self::assertSame(self::$Message->getSendState(), $found->getSendState());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Test that a message is not found when it doesn't exist
     */
    public function testFindByIdReturnNull()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $messageId = -2;
            $found = Message::findById($messageId);

            self::assertNull($found);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    /**
     * Test that a list of message from a specific date
     */
    public function testFindByDateReturnMessage()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $date = self::$Message->getSendDate();
            $found = Message::findByDate($date);

            self::assertIsArray($found);
            self::assertNotEmpty($found);
            self::assertSame(self::$Message->getMessageId(), $found[0]->getMessageId());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that an empty list of message is returned when the date is not registered
     */
    public function testFindByDateReturnEmpty()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $date = "2015-10-05";
            $found = Message::findByDate($date);

            self::assertIsArray($found);
            self::assertEmpty($found);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that a list of message is returned from a specific discussion
     */
    public function testFindByDiscussionReturnMessage()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $discussionId = self::$Discussion->getDiscussionId();
            $found = Message::findByDiscussion($discussionId);

            self::assertIsArray($found);
            self::assertNotEmpty($found);
            self::assertSame($discussionId, $found[0]->getDiscussionId());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that an empty list of message is returned when the discussion doesn't exist
     */
    public function testFindByDiscussionReturnEmpty()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $discussionId = -2;
            $found = Message::findByDiscussion($discussionId);

            self::assertIsArray($found);
            self::assertEmpty($found);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that a list of message is returned according to a specific sender
     */
    public function testFindBySenderReturnMessage()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $senderId = self::$Customer->getUserId();
            $found = Message::findBySender($senderId);

            self::assertIsArray($found);
            self::assertNotEmpty($found);
            self::assertSame($senderId, $found[0]->getSender());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that a list of message is returned empty when the sender doesn't exist
     */
    public function testFindBySenderReturnEmpty()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $senderId = -2;
            $found = Message::findBySender($senderId);

            self::assertIsArray($found);
            self::assertEmpty($found);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that a list of message is returned according to a specific receiver
     */
    public function testFindByReceiverReturnMessage()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $receiverId = self::$Seller->getUserId();
            $found = Message::findByReceiver($receiverId);

            self::assertIsArray($found);
            self::assertNotEmpty($found);
            self::assertSame($receiverId, $found[0]->getReceiver());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that a list of message is returned empty when the receiver doesn't exist
     */
    public function testFindByReceiverReturnEmpty()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $receiverId = -2;
            $found = Message::findByReceiver($receiverId);

            self::assertIsArray($found);
            self::assertEmpty($found);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that a list of all message is returned
     */
    public function testFindAllMessage()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $found = Message::findAll();

            self::assertIsArray($found);
            self::assertNotEmpty($found);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test that an empty list of message is return when there is no messages
     */
    public function testFindNoMessage()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            Database::query("DELETE FROM messages");
            $found = Message::findAll();

            self::assertIsArray($found);
            self::assertEmpty($found);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test create a message without required informations
     */
    public function testCreateInvalidMessage()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");

            $invalid = new Message();
            $invalid->create();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test create a message without content
     */
    public function testCreateMessageWithoutContent()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");

            $invalid = new Message();
            $invalid->setReadState(FALSE)
            ->setDiscussionId(self::$Discussion->getDiscussionId())
            ->setSender(self::$Customer->getUserId())
            ->setReceiver(self::$Seller->getUserId())
            ->create();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Test create message without discussion ID
     */
    public function testCreateMessageWithoutDiscussionID()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");

            $invalid = new Message();
            $invalid->setContent("nejgnoaengobgbgn")
                ->setReadState(FALSE)
                ->setSender(self::$Customer->getUserId())
                ->setReceiver(self::$Seller->getUserId());
            $invalid->create();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test create a message without sender/receiver ID
     */
    public function testCreateMessageWithoutUserId()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");

            $invalid = new Message();
            $invalid->setContent("nejgnoaengobgbgn")
                ->setReadState(FALSE)
                ->setDiscussionId(self::$Discussion->getDiscussionId());
            $invalid->create();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * test verify that the message is sent and not delivered or seen
     */
    public function testMessageIsSent()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            $found = Message::findById(self::$Message->getMessageId());

            self::assertTrue($found->getSendState());
            self::assertFalse($found->getDeliveredState());
            self::assertFalse($found->getReadState());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * test a created message is sent and delivered but not seen
     */
    public function testMessageIsDelivered()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }
            
            self::$Message->markAsDelivered();
            $found = Message::findById(self::$Message->getMessageId());

            self::assertTrue($found->getSendState());
            self::assertTrue($found->getDeliveredState());
            self::assertFalse($found->getReadState());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * test a created message is sent, delivered and seen
     */
    public function testMessageIsRead()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }
            self::$Message->markAsDelivered();
            self::$Message->markAsSeen();
            $found = Message::findById(self::$Message->getMessageId());

            self::assertTrue($found->getSendState());
            self::assertTrue($found->getDeliveredState());
            self::assertTrue($found->getReadState());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * test update a message with valid informations
     */
    public function testUpdateValidMessage()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            self::$Message->markAsDelivered();
            $update = self::$Message->setContent("nejgnoaengobgbgn")
                ->setReadState(TRUE)
                ->setDiscussionId(self::$Discussion->getDiscussionId())
                ->setSender(self::$Seller->getUserId())
                ->setReceiver(self::$Customer->getUserId());
                
            $update->update();

            $found = Message::findById($update->getMessageId());

            self::assertInstanceOf(Message::class, $update);
            self::assertSame($update->getDiscussionId(), $found->getDiscussionId());
            self::assertSame($update->getContent(), $found->getContent());
            self::assertNotSame($update->getSender(), $found->getSender());
            self::assertNotSame($update->getReceiver(), $found->getReceiver());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test update a message with invalid information
     */
    public function testUpdateInvalidMessage()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");

            $update = self::$Message->setContent("")
                ->setReadState(TRUE);
            $update->update();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test delete an existing message
     */
    public function testDeleteExistingMessage()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            self::assertTrue(self::$Message->delete());
            self::assertNull(Message::findById(self::$Message->getMessageId()));

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Test delete a non existing message
     */
    public function testDeleteNonExistingMessage()
    {
        try {
            if (!Database::isConnected()) {
                throw new Exception("ERROR : database is not connected");
            }

            self::expectException(Exception::class);
            self::expectExceptionMessageMatches("/VALIDATION ERROR/");
            self::$Message->setMessageId(-2)->delete();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}