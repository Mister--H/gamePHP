<?php

namespace App\Controllers;

use App\Models\GameModel;
use App\Models\Database;

class GameController
{
    protected $auth;
    private $GameModel;
    public function __construct()
    {
        // Assuming Database class takes care of its own configuration
        $database = new Database();
        $this->GameModel = new GameModel($database);

    }

    public function setPosition()
    {
        header('Content-Type: application/json'); // Ensure the content type is set to JSON

        $requestData = file_get_contents('php://input');
        $data = json_decode($requestData, true);

        if ($data === null || !isset($data['lat'], $data['lng'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid or missing lat/lng data']);
            return; // Important to stop further execution
        }

        $lng = $data['lng'];
        $lat = $data['lat'];
        $user_id = $data['user_id'];

        $result = $this->GameModel->updatePosition($lng, $lat, $user_id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Position updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update position']);
        }
    }



    public function getPosition()
    {
        $response = $this->GameModel->getPosition();

        // Check if the response is successful
        if ($response !== false) {
            // Return the position as JSON
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $response]);
            exit(); // Terminate script after sending JSON response
        } else {
            // Handle the error, perhaps log it or set a default value for LastPosition
            $error = 'error';
            // For example, setting a default position
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $error]);
            exit(); // Terminate script after sending JSON response
        }
    }

    public function getNearbyPlayersPosition()
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        $lat = $data['lat'] ?? 0;  // Use null coalescing operator to provide default
        $lng = $data['lng'] ?? 0;  // Use null coalescing operator to provide default
        $userId = $data['userId'] ;
        $nearbyPlayers = $this->GameModel->getNearbyPlayers($lat, $lng, $userId);

        header('Content-Type: application/json');
        if (!$nearbyPlayers) {
            echo json_encode(['success' => false, 'message' => 'No nearby players found']);
        } else {
            echo json_encode(['success' => true, 'data' => $nearbyPlayers]);
        }
        exit();
    }





}
