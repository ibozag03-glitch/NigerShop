<?php
/**
 * Test class for the Router component.
 * 
 * This test suite verifies the functionality of the Router class including:
 * - Router initialization and instantiation
 * - HTTP method routing (GET, POST, PUT, DELETE)
 * - URL generation with and without parameters
 * - Route matching for existing and non-existing routes
 * - Error handling for route not found scenarios
 * 
*/

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config/config.php';

use PHPUnit\Framework\TestCase;
use App\Core\Router;

final class RouterTest extends TestCase
{
    private Router $router;
    private string $testBasePath;

    /**
     * Set up test fixtures before each test method.
     * Initializes a new Router instance with the test base path.
     */
    protected function setUp(): void
    {
        $this->testBasePath = PUBLIC_REP;
        $this->router = new Router($this->testBasePath);
    }

    /**
     * Test that the Router initializes correctly.
     * 
     * Verifies that the Router constructor returns an instance of the Router class.
     */
    public function testInitializeRouter(): void
    {
        self::assertInstanceOf(Router::class, $this->router);
    }

    /**
     * Test GET route registration.
     * 
     * Verifies that the get() method returns the Router instance for method chaining
     * when registering a GET route.
     */
    public function testGetRouting(): void
    {
        $get = $this->router->get('/', 'homePage', 'home');
        self::assertInstanceOf(Router::class, $get);
    }

    /**
     * Test POST route registration.
     * 
     * Verifies that the post() method returns the Router instance for method chaining
     * when registering a POST route.
     */
    public function testPostRouting(): void
    {
        $post = $this->router->post('/annonces', 'annonces/index', 'annonces');
        self::assertInstanceOf(Router::class, $post);
    }

    /**
     * Test PUT route registration.
     * 
     * Verifies that the put() method returns the Router instance for method chaining
     * when registering a PUT route.
     */
    public function testPutRouting(): void
    {
        $put = $this->router->put('/annonces', 'annonces/index', 'annonces');
        self::assertInstanceOf(Router::class, $put);
    }

    /**
     * Test DELETE route registration.
     * 
     * Verifies that the delete() method returns the Router instance for method chaining
     * when registering a DELETE route.
     */
    public function testDeleteRouting(): void
    {
        $delete = $this->router->delete('/annonces', 'annonces/index', 'annonces');
        self::assertInstanceOf(Router::class, $delete);
    }

    /**
     * Test URL generation for static routes.
     * 
     * Verifies that the url() method generates the correct URL for a registered route
     * without parameters.
     */
    public function testUrlGeneration(): void
    {
        $this->router->get('/annonces', 'annonces/index', 'annonces');
        
        $url = $this->router->url('annonces');
        self::assertEquals('/annonces', $url);
    }

    /**
     * Test URL generation for routes with parameters.
     * 
     * Verifies that the url() method correctly replaces placeholders with provided
     * parameter values when generating URLs.
     */
    public function testUrlGenerationWithParams(): void
    {
        $this->router->get('/user-[i:id]', 'user/profile', 'user.profile');
        
        $url = $this->router->url('user.profile', ['id' => 123]);
        $this->assertEquals('/user-123', $url);
    }

    /**
     * Test GET route matching.
     * 
     * Verifies that a GET route correctly matches when a GET request is made
     * to the registered path, returning the expected target and route name.
     */
    public function testRouteMatchingGet(): void
    {
        $this->router->get('/', 'homePage', 'home');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];

        $match = $this->router->router->match();
        
        self::assertNotFalse($match);
        self::assertEquals('homePage', $match['target']);
        self::assertEquals('home', $match['name']);
    }

    /**
     * Test POST route matching.
     * 
     * Verifies that a POST route correctly matches when a POST request is made
     * to the registered path, returning the expected target and route name.
     */
    public function testRouteMatchingPost(): void
    {
        $this->router->post('/', 'homePage', 'home');

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [];

        $match = $this->router->router->match();
        
        self::assertNotFalse($match);
        self::assertEquals('homePage', $match['target']);
        self::assertEquals('home', $match['name']);
    }

    /**
     * Test route non-matching for incorrect path.
     * 
     * Verifies that when a route is registered for one path but a request is made
     * to a different path, the router correctly returns false indicating no match.
     */
    public function testNotMatchingRoute(): void
    {
        $this->router->get('/azeaze', 'homePage', 'home');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];

        $match = $this->router->router->match();
        
        self::assertFalse($match);
    }

    /**
     * Test route not found when no routes are registered.
     * 
     * Verifies that when no routes have been registered at all, the router's
     * match() method returns false for any request.
     */
    public function testRouteNotFound(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];

        $match = $this->router->router->match();
        self::assertFalse($match);
    }

    /**
     * Test route handling for existing and matching file/route.
     * 
     * Verifies that when a route exists and the request method matches the
     * registered route method, the router correctly identifies and returns
     * the route information including target and name.
     */
    public function testRoutingWithExistingFile(): void
    {
        $this->router->get('/register', 'auth/register', 'inscription');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = ['url' => '/register'];

        $match = $this->router->router->match();
        self::assertNotFalse($match);
        self::assertEquals('auth/register', $match['target']);
        self::assertEquals('inscription', $match['name']);
    }

    /**
     * Test route handling for non-existing file/route.
     * 
     * Verifies that when no route exists for the requested path, the router's
     * run() method throws an Exception with the appropriate "route not found" message.
     */
    public function testRoutingWithNonExistingFile(): void
    {
        // Don't register any route for /nonexisting
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = ['url' => '/nonexisting'];

        self::expectException(Exception::class);
        self::expectExceptionMessage("Aucune route ne correspond à l'Url demandée.");

        $this->router->run();
    }
}