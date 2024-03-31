<?php

namespace App\Models;

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use MongoDB\Client as MongoClient; // Make sure to use this line


class Database {
    private $client; // MongoDB client
    private $db;
    private $logger; // Monolog logger

    public function __construct() {
        // Initialize Dotenv and load environment variables
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..'); // Adjust the path to your .env file
        $dotenv->load();

        // Initialize Monolog logger
        $this->logger = new Logger('database');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/database.log', Logger::WARNING));

        // Create a new MongoDB client instance
        try {
           $this->client = new MongoClient($_ENV['MONGODB_URI']);
           $this->db = $this->client->selectDatabase($_ENV['DB_NAME']);
        } catch (\Throwable $e) { // Catching all errors and exceptions
            $this->logger->error('Database connection error: ' . $e->getMessage());
            throw $e; // Re-throwing the exception after logging it
        }
    }

     // Method to get MongoDB collection
    public function getCollection($collectionName) {
        return $this->db->selectCollection($collectionName);
    }

}
    
    
?>