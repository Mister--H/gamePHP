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

    public function updatePosition($lng, $lat, $user_id)
    {
        try {
            $collection = $this->db->getCollection('user_status');

            // Check if a document exists for the current user_id
            $documentCount = $collection->countDocuments(['user_id' => $user_id]);

            if ($documentCount === 0) {
                // If no document exists, insert a new document with default values
                $initialDocument = [
                    'user_id' => $user_id,
                    'coins' => 0.0,
                    'lastPosition' => [
                        'type' => 'Point',
                        'coordinates' => [(float) $lng, (float) $lat]
                    ],
                    'ownedPlaces' => [],
                    'status' => ''
                ];
                $collection->insertOne($initialDocument);
            }

            // Prepare the filter to find the document for the current user_id
            $filter = ['user_id' => $user_id];

            // Prepare the updated document
            $updatedDocument = [
                '$set' => [
                    'lastPosition.coordinates' => [(float) $lng, (float) $lat]
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
            $document = $collection->findOne(['user_id' => $_SESSION['user_id']]);

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

    public function getNearbyPlayers($lat, $lng, $userId){
        try {
            $radius = 1000;
            $collection = $this->db->getCollection('user_status');
            $currentUser = $userId;

            // $this->logger->error('Excluding user ID: ' . $currentUser);  // Log current user ID being excluded


            $filter = [
                'lastPosition' => [
                    '$nearSphere' => [
                        '$geometry' => [
                            'type' => 'Point',
                            'coordinates' => [(float) $lng, (float) $lat]
                        ],
                        '$maxDistance' => $radius
                    ]
                    ],
                'user_id' => ['$ne' => $currentUser]

            ];

            // Log the filter to debug
            // $this->logger->error('Executing nearby search with filter: ' . json_encode($filter));

            $documents = $collection->find($filter);
            $nearbyPlayers = [];

            foreach ($documents as $document) {
                $nearbyPlayers[] = [
                    'user_id' => $document['user_id'],
                    'lat' => $document['lastPosition']['coordinates'][1],
                    'lng' => $document['lastPosition']['coordinates'][0]
                ];
            }

            // // Log the result count
            // $this->logger->error('Found ' . count($nearbyPlayers) . ' nearby players.');

            return $nearbyPlayers;
        } catch (\Exception $e) {
            $this->logger->error('Error retrieving nearby players: ' . $e->getMessage());
            return false;
        }
    }




}