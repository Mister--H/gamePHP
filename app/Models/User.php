<?php
namespace App\Models;

// Ensure you're using the correct Database class that has been adapted for MongoDB.
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectId;

class User
{
    private $db; // This will be an instance of your custom Database class for MongoDB.
    private $logger;
    private $objectid;

    public function __construct(Database $db)
    {
        $this->db = $db; // Database instance is injected via the constructor.
        $this->objectid = new ObjectId;
        // Initialize Monolog logger for UserModel
        $this->logger = new Logger('UserModel');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/usermodel.log', Logger::WARNING));
    }

    public function findUserByEmail($email)
    {
        $collection = $this->db->getCollection('users');
        $result = $collection->findOne(['email' => $email], ['projection' => ['id' => 1]]);

        return $result;
    }
    public function register($name, $email, $password)
    {
        $collection = $this->db->getCollection('users');

        // Prepare the document to insert
        $userDocument = [
            'email' => $email,
            'password' => $password, // Hashing the password for security
            'coins' => 100, // Starting coins
            'ownedPlaces' => [], // Array to store place IDs
            'firstName' => '',
            'lastName' => '',
            'nickname' => $name,
            'sex' => '',
            'age' => '',
            'phoneNumber' => '',
            'instagram' => '',
            'facebook' => '',
            'twitter' => '',
            'telegram' => '',
            'bio' => '',
            'avatar' => '',
        ];

        // Execute the insert operation
        $result = $collection->insertOne($userDocument);

        if ($result->getInsertedCount() == 1) {
            return $result->getInsertedId(); // Return the MongoDB ObjectId of the inserted document
        } else {
            return false; // Return false if insert failed
        }
    }

    public function verifyUserCredentials($email, $password)
    {
        $collection = $this->db->getCollection('users');
        $user = $collection->findOne(['email' => $email]);
        // $pass = password_verify($password, $user['password']);
        // return $pass;
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Return the user data if credentials are valid
        } else {
            return false; // Return false if credentials are invalid
        }
    }

    public function storeRememberToken($userId, $token)
    {
        $collection = $this->db->getCollection('remember_tokens');
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        $expiresAt = new UTCDateTime(strtotime('+30 days') * 1000); // Token expires in 30 days, MongoDB expects UTCDateTime

        $result = $collection->insertOne([
            'user_id' => $userId,
            'token' => $hashedToken,
            'expires_at' => $expiresAt,
        ]);
    }



    public function validateRememberToken($token)
    {
        $collection = $this->db->getCollection('remember_tokens');
        $currentDate = new MongoDB\BSON\UTCDateTime(time() * 1000); // Current time in UTCDateTime format

        $tokens = $collection->find([
            'expires_at' => ['$gt' => $currentDate] // Find tokens that have not expired
        ]);

        foreach ($tokens as $tokenRow) {
            if (password_verify($token, $tokenRow['token'])) {
                return $tokenRow['user_id']; // Return user ID if token is valid
            }
        }

        return false;
    }
    public function getUserById($userId)
    {
        $collection = $this->db->getCollection('users');

        $user = $collection->findOne(['_id' => $this->objectid($userId)]);
        return $user;
    }

    public function saveProfile($email, $password, $firstName, $lastName, $nickname, $sex, $age, $phoneNumber, $instagram, $facebook, $twitter, $telegram, $bio, $avatar)
    {
        $collection = $this->db->getCollection('users');

        // Prepare the document update query
        $updateQuery = [
            '$set' => [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'nickname' => $nickname,
                'sex' => $sex,
                'age' => $age,
                'phoneNumber' => $phoneNumber,
                'instagram' => $instagram,
                'facebook' => $facebook,
                'twitter' => $twitter,
                'telegram' => $telegram,
                'bio' => $bio,
                'avatar' => $avatar,
            ]
        ];

        // Check if password is provided and update it
        if (!empty($password)) {
            $updateQuery['$set']['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT); // You may want to hash the password here for security
        }
        if (!empty($email) && $email !== $_SESSION['user']['email']) {
            $updateQuery['$set']['email'] = $email;
        }

        // Execute the update operation
        $result = $collection->updateOne(
            ['email' => $email], // Match the user document by email
            $updateQuery // Update with the prepared query
        );
        if ($result === false) {
            return ['error' => 'An error occurred while updating the user profile.'];
        }
        
        $user = $collection->findOne(['email' => $email]); // Update the user session data

        return $user; // Return the number of documents modified
    }







}