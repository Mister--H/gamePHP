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
        // Get the raw JSON data from the request body
        $requestData = file_get_contents('php://input');

        // Decode the JSON data into an associative array
        $data = json_decode($requestData, true);

        // Check if the JSON data is valid
        if ($data === null || !isset($data['lat'], $data['lng'])) {
            // If JSON data is invalid or lat/lng is missing, return an error response
            return json_encode(['success' => false, 'message' => 'Invalid or missing lat/lng data']);
        }

        // Extract latitude and longitude from the JSON data
        $lat = $data['lat'];
        $lng = $data['lng'];

        // Pass data to the gameModel
        $result = $this->GameModel->updatePosition($lat, $lng);

        // Check if the update was successful
        if ($result) {
            // If successful, return a success response
            return json_encode(['success' => true, 'message' => 'Position updated successfully']);
        } else {
            // If unsuccessful, return an error response
            return json_encode(['success' => false, 'message' => 'Failed to update position']);
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
            $LastPosition = ['lat' => 0, 'lng' => 0];
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $error]);
            exit(); // Terminate script after sending JSON response
        }
    }



}
