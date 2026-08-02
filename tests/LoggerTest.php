<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config/config.php';

use App\Core\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

final class LoggerTest extends TestCase {

    private static string $testLogDir;
    
    /**
     * Set up fixtures before each test
     */
    protected function setUp(): void
    {
        self::$testLogDir = NS_REP . 'logs';
        mkdir(self::$testLogDir, 0777, true);

        Logger::init(self::$testLogDir);
    }

    /**
     * Clean up after each test - remove temporary log files
     */
    protected function tearDown(): void
    {
        if (is_dir(self::$testLogDir)) {
            array_map('unlink', glob(self::$testLogDir . DIRECTORY_SEPARATOR . "*.log"));
            rmdir(self::$testLogDir);
        }
        logger::$initialized = false;
    }

    /**
     * Test that logger can be initialized
     */
    public function testLoggerInitialisation()
    {
        //Logger::init(self::$testLogDir);

        self::assertTrue(Logger::isInitialized());
        self::assertEquals(self::$testLogDir, Logger::$logDir);
    }

    /**
     * Test logging without context data and with all levels
     */
    public function testLoggingWithoutContext()
    {
        $levelsAndExpected = [                                                    
            LogLevel::INFO => '[INFO]',
            LogLevel::NOTICE => '[NOTICE]',
            LogLevel::WARNING => '[WARNING]',
            LogLevel::ERROR => '[ERROR]',
            LogLevel::EMERGENCY => '[EMERGENCY]',
            LogLevel::ALERT => '[ALERT]',
            LogLevel::CRITICAL => '[CRITICAL]',
            LogLevel::DEBUG => '[DEBUG]'
        ];
        
        foreach ($levelsAndExpected as $level => $expectedMarker) {
            $testMessage = 'Generic log test for ' . $level;
            Logger::log($level, $testMessage);

            $logFile = Logger::getLogFilename();
            $logPath = self::$testLogDir . DIRECTORY_SEPARATOR . $logFile;
            $logContents = file_get_contents($logPath);

            self::assertFileExists($logPath);
            self::assertStringContainsString($testMessage, $logContents);
            self::assertStringContainsString($expectedMarker, $logContents);
        }
    }

    /**
     * Test logging with context data
     */
    public function testLoggingWithContext()
    {
        $message = "User is Logged in";
        $context = [
            "Username" => "ibozag",
            "email" => "leamsiibozag@gmail.com",
            "phone" => "+277 86 61 20 16",
            "birth_date" => "07/02/2007",
            'path' => '/annonces/456/edit',                                       
            'timestamp' => '2026-08-01 17:55:00'
        ];
        Logger::info($message, $context);

        $logFile = Logger::getLogFilename();
        $logPatth = self::$testLogDir . DIRECTORY_SEPARATOR . $logFile;
        $logContent = file_get_contents($logPatth);

        self::assertFileExists($logPatth);
        self::assertStringContainsString($message, $logContent);
        self::assertStringContainsString("[INFO]", $logContent);
        self::assertStringContainsString("\"Username\":\"ibozag\"", $logContent);
        self::assertStringContainsString('"path":"/annonces/456/edit"', $logContent);
    }

    /**
     * Test that multiple log entries are appended correctly
     */
    public function testLoggingWithMutipleEntries()
    {
        $firstMessage = 'First log entry of the test';
        $secondMessage = 'Second log entry of the test';
        $thirdMessage = 'Third log entry with different level';
  
        Logger::info($firstMessage);
        Logger::error($secondMessage);
        Logger::debug($thirdMessage);
  
        $logFile = Logger::getLogFilename();
        $logPath = self::$testLogDir . DIRECTORY_SEPARATOR . $logFile;

        self::assertFileExists($logPath);
        $logContents = file_get_contents($logPath);
  
        // Verify all three messages appear
        self::assertStringContainsString($firstMessage, $logContents);
        self::assertStringContainsString($secondMessage, $logContents);
        self::assertStringContainsString($thirdMessage, $logContents);
  
        // Verify all three level markers appear
        self::assertStringContainsString('[INFO]', $logContents);
        self::assertStringContainsString('[ERROR]', $logContents);
        self::assertStringContainsString('[DEBUG]', $logContents);
  
        $lineCount = substr_count($logContents, PHP_EOL);
        self::assertGreaterThanOrEqual(3, $lineCount);
    }
}