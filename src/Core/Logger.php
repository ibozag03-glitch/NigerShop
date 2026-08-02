<?php
/**
 * Logger class for NigerShop application
 * Handles logging of user actions, admin actions, and debug informations
*/
namespace App\Core;

use DateTimeImmutable;
use Psr\Log\LogLevel;

class Logger {

    public static string $logDir;
    private static string $filePrefix = "nigershop";
    private static int $dirPerm = 0755;
    public static bool $initialized = false;

    /**
     * Initialize the logger with configuration
     * 
     * @param string $logDir Directory where log files will be stored
     */
    public static function init(string $logDir = ''): void
    {
        self::$logDir = rtrim($logDir, DIRECTORY_SEPARATOR);

        if (!empty(self::$logDir) && !is_dir(self::$logDir)) {
            mkdir(self::$logDir, self::$dirPerm, true);
        }

        self::$initialized = true;
    }

    /**
     * Verifies if the Logger is initialized
     */
    public static function isInitialized(): bool
    {
        return self::$initialized ? true : false;
    }

    /**
     * Get log filename based on current date
     * 
     * @return string Log filename (e.g., nigershop_2026-08-01.log)
     */
    public static function getLogFilename(): string
    {
        $date = (new DateTimeImmutable())->format('Y-m-d');
        return sprintf("%s_%s.log", self::$filePrefix, $date);
    }

    /**
     * Write log entry to file
     * 
     * @param string $logEntry Formatted log entry to write
     */
    public static function writeToLog(string $logEntry): void
    {
        if (empty(self::$logDir)) {
            return;
        }

        $logFile = self::getLogFilename();
        $filePath = self::$logDir . DIRECTORY_SEPARATOR . $logFile;

        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, self::$dirPerm, true);
        }

        file_put_contents($filePath, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Generic log method
     * 
     * @param string $level Log level
     * @param string $message Message to log
     * @param array $context Contextual information
     */
    public static function log(mixed $level, string $message, array $context = []): void
    {
        if (!self::$initialized) {
            return;
        }

        $timestamp = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $level = strtoupper($level);

        $contextStr = '';
        if (!empty($context)) {
            $contextStr = " | Context: " . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $logEntry = sprintf("[%s] [%s] : %s%s%s", $timestamp, $level, $message, $contextStr, PHP_EOL);

        self::writeToLog($logEntry);
    }

    /**
     * The following methods are all based on the generic log method
     * They add specification about what has been logged
     */

    public static function info(string $message, array $context = []): void
    {
        self::log(LogLevel::INFO, $message, $context);
    }

    public static function notice(string $message, array $context = []): void
    {
        self::log(LogLevel::NOTICE, $message, $context);
    }
    
    public static function warning(string $message, array $context = []): void
    {
        self::log(LogLevel::WARNING, $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log(LogLevel::ERROR, $message, $context);
    }
    
    public static function emergency(string $message, array $context = []): void
    {
        self::log(LogLevel::EMERGENCY, $message, $context);
    }

    public static function alert(string $message, array $context = []): void
    {
        self::log(LogLevel::ALERT, $message, $context);
    }

    public static function critical(string $message, array $context = []): void
    {
        self::log(LogLevel::CRITICAL, $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::log(LogLevel::DEBUG, $message, $context);
    }
}