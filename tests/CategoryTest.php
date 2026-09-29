<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "config/config.php";

use App\Core\Database;
use App\Models\Category;
use PHPUnit\Framework\TestCase;

final class CategoryTest extends TestCase {

    private static Category $Category;
    private static array $category = [
        "name" => "Testing Category Name",
        "slug" => "testing-category-slug",
        "descriptions" => "Testing category name, slug and description"
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
     * Create setUp data in the Category object to insert in the database
     */
    public function setTestCategory(): Category
    {
        $category = new Category();
        $category->setName(self::$category['name'])
            ->setSlug(self::$category['slug'])
            ->setDescriptions(self::$category['descriptions']);
        return $category;

    }

    /**
     * Delete the setup data from the database
     */
    public function tearDown(): void
    {
        if (Database::isConnected()) {
            Database::query("DELETE FROM categories WHERE slug LIKE :slug", ["slug" => "testing-%"]);
        }
    }

    /**
     * test if the category doesn't exist
     */
    public function testFindCategoryByIdReturnsNull()
    {
        try {
            if (Database::isConnected()) {
                $NotFoundedCategory = Category::findById(9999);            
                self::assertNull($NotFoundedCategory);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if the category does exist
     */
    public function testFindCategoryByIdReturnsCategory()
    {
        try {
            if (Database::isConnected()) {
                self::$Category = $this->setTestCategory();
                self::$Category->create();
                $categoryId = self::$Category->getCategoryId();
                $foundedCategory = Category::findById($categoryId);
                self::assertInstanceOf(Category::class, $foundedCategory);
                self::assertSame($categoryId, $foundedCategory->getCategoryId());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if the category search returns null when there is no name set
     */
    public function testFindCategoryByNameReturnsNull()
    {
        try {
            if (Database::isConnected()) {
                self::$Category = $this->setTestCategory();
                $categoryName = self::$Category->getName();
                $NotFoundedCategory = Category::findByName($categoryName);            
                self::assertNull($NotFoundedCategory);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if the category can be find by its name
     */
    public function testFindCategoryByNameReturnsCategory()
    {
        try {
            if (Database::isConnected()) {
                self::$Category = $this->setTestCategory();
                self::$Category->create();
                $categoryName = self::$Category->getName();
                $foundedCategory = Category::findByName($categoryName);
                self::assertInstanceOf(Category::class, $foundedCategory);
                self::assertSame($categoryName, $foundedCategory->getName());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if the category search returns null when there is no slug set
     */
    public function testFindCategoryBySlugReturnsNull()
    {
        try {
            if (Database::isConnected()) {
                self::$Category = $this->setTestCategory();
                $categorySlug = self::$Category->getSlug();
                $NotFoundedCategory = Category::findBySlug($categorySlug);            
                self::assertNull($NotFoundedCategory);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if the category can be find by its slug
     */
    public function testFindCategoryBySlugReturnsCategory()
    {
        try {
            if (Database::isConnected()) {
                self::$Category = $this->setTestCategory();
                self::$Category->create();
                $categorySlug = self::$Category->getSlug();
                $foundedCategory = Category::findBySlug($categorySlug);
                self::assertInstanceOf(Category::class, $foundedCategory);
                self::assertSame($categorySlug, $foundedCategory->getSlug());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if the function find all category and put them into an array
     */
    public function testFindAllCategories()
    {
        try {
            if (Database::isConnected()) {
                self::$Category = $this->setTestCategory();
                self::$Category->create();
                $categories = Category::findAll();
                self::assertIsArray($categories);
                self::assertNotEmpty($categories);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if the function find no categories when they don't exist
     */
    public function testFindNoCategories()
    {
        try {
            if (Database::isConnected()) {
                Database::query("DELETE FROM categories");
                $noCategories = Category::findAll();
                self::assertEmpty($noCategories);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }
    
    /**
     * test if the category can be created with valid data
     */
    public function testCreateValidCategory()
    {
        try {
            if (Database::isConnected()) {
                self::$Category = $this->setTestCategory();
                self::assertTrue(self::$Category->create());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if the category won't be created with invalid data
     */
    public function testCreateInvalidCategory()
    {
        try {
            if (Database::isConnected()) {
                $invalidCategoryData = (new Category())
                    ->setName("")
                    ->setSlug("")
                    ->setDescriptions("");

                self::expectException(Exception::class);
                self::expectExceptionMessageMatches('/VALIDATION ERROR/');
                $invalidCategoryData->create();
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if the category will be updated with valid data
     */
    public function testUpdateValidCategory()
    {
        try {
            if (Database::isConnected()) {
                self::$Category = $this->setTestCategory();
                self::$Category->create();

                self::$Category->setName("Testing Update Name")
                    ->setSlug("testing-update-slug")
                    ->setDescriptions("Testing Update category with valid name, slug and descriptions");
                $updatedCategory = self::$Category->update();
                
                self::assertInstanceOf(Category::class, self::$Category);
                self::assertTrue($updatedCategory);
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if the category won't be updated with invalid data
     */
    public function testUpdateInvalidCategory()
    {
        try {
            if (Database::isConnected()) {
                self::$Category = $this->setTestCategory();
                self::$Category->create();

                //update data
                $updatedCategory = self::$Category->setName("")
                    ->setSlug("")
                    ->setDescriptions("");
                
                //check the updates
                self::expectException(Exception::class);
                self::expectExceptionMessageMatches('/VALIDATION ERROR/');
                $updatedCategory->update();
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if a category can be delete when it exist
     */
    public function testDeleteExistingCategory()
    {
        try {
            if (Database::isConnected()) {
                self::$Category = $this->setTestCategory();
                self::$Category->create();
                self::assertTrue(self::$Category->delete());
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

    /**
     * test if the function can not delete a non existing category
     */
    public function testDeleteNonExistingCategory()
    {
        try {
            if (Database::isConnected()) {
                self::$Category = $this->setTestCategory();
                self::$Category->setCategoryId(9999);

                self::expectException(Exception::class);
                self::expectExceptionMessageMatches('/FAIL to delete/');
                self::$Category->delete();
            }
        } catch (Exception $e) {
            throw new Exception("ERROR : " . $e->getMessage());
        }
    }

}