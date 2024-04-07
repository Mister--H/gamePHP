<?php
namespace App\Models;

// Ensure you're using the correct Database class that has been adapted for MongoDB.
use MongoDB\Collection;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class GameModel
{
    private $db; // MongoDB db instance.
    private $logger;
    

    public function __construct(Database $db)
    {
        $this->db = $db; // MongoDB db instance is injected via the constructor.

        // Initialize Monolog logger for GameModel
        $this->logger = new Logger('GameModel');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/gamemodel.log', Logger::WARNING));
    }

    public function updatePosition($lat, $lng)
    {
        try {
            $collection = $this->db->getCollection('user_status');

            // Check if a document exists for the current user_id
            $documentCount = $collection->countDocuments(['user_id' => $_SESSION['user_id']]);

            if ($documentCount === 0) {
                // If no document exists, insert a new document with default values
                $initialDocument = [
                    'user_id' => $_SESSION['user_id'],
                    'coins' => 0.0,
                    'lastPosition' => ['lat' => 0.0, 'lng' => 0.0],
                    'ownedPlaces' => [],
                    'status' => ''
                ];
                $collection->insertOne($initialDocument);
            }

            // Prepare the filter to find the document for the current user_id
            $filter = ['user_id' => $_SESSION['user_id']];

            // Prepare the updated document
            $updatedDocument = [
                '$set' => [
                    'lastPosition.lat' => (float) $lat,
                    'lastPosition.lng' => (float) $lng
                ]
            ];

            // Update the position in the "user_status" collection
            $result = $collection->updateOne($filter, $updatedDocument);

            if ($result->getModifiedCount() > 0) {
                // If the position was updated successfully, return true
                return true;
            } else {
                // If no documents were modified, log a warning and return false
                $this->logger->warning('No documents matching the filter criteria were found for position update.');
                return false;
            }
        } catch (\Exception $e) {
            // If an exception occurs, log the error and return false
            $this->logger->error('Error updating position: ' . $e->getMessage());
            return false;
        }
    }


    public function getPosition()
    {
        try {
            $collection = $this->db->getCollection('user_status');

            // Retrieve the document from the collection
            $document = $collection->findOne(['user_id'=> $_SESSION['user_id']]);

            if ($document) {
                // If the document exists, return the lastPosition as an associative array
                return $document['lastPosition'];
            } else {
                // If no document was found, log a warning and return false
                $this->logger->warning('No document found in user_status collection.');
                return false;
            }
        } catch (\Exception $e) {
            // If an exception occurs, log the error and return false
            $this->logger->error('Error retrieving position: ' . $e->getMessage());
            return false;
        }
    }


}
