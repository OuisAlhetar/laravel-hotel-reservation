<?php

namespace App\Singleton;

class Logger
{
    private static $instance = null;

    // Private constructor to prevent creating multiple instances
    private function __construct() {}

    // Public method to get the single instance of the Logger
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Logger();
        }

        return self::$instance;
    }

    // Method to log messages to a file
    public function log($message)
    {
        // Log messages to custom_log.log in the storage/logs directory
        file_put_contents(storage_path('logs/custom_log.log'), $message . PHP_EOL, FILE_APPEND);
    }
}