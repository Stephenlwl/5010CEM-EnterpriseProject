<?php

class User {

    // Database connection and table name
    private $conn;
    private $table_name = "users";
    // Object properties
    public $id;
    public $username;
    public $email;
    public $password;

    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create user
    function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                  SET Username=:username, Email=:email, Password=:password";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);

        return false;
    }

    public function checkCredentials($email, $password) {
        // Update the query to use Email instead of Username
        $query = "SELECT * FROM " . $this->table_name . " WHERE Email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        // Bind the email parameter
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Check if user exists and verify the password
        if ($user && password_verify($password, $user['Password'])) {
            return [
                'UserID' => $user['UserID'],
                'Username' => $user['Username'],
            ];        
        } else {
            return false; 
        }
    }
    
    
    public function updateName($id, $newName) {
        $query = "UPDATE " . $this->table_name . " SET Username = :Username, UserUpdatedAt = NOW() WHERE UserID = :UserID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':Username', $newName);
        $stmt->bindParam(':UserID', $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updatePhoneNumber($id, $newPhoneNumber) {
        $query = "UPDATE " . $this->table_name . " SET PhoneNumber = :PhoneNumber, UserUpdatedAt = NOW() WHERE UserID = :UserID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':PhoneNumber', $newPhoneNumber);
        $stmt->bindParam(':UserID', $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    public function updatePassword($id, $newPassword) {
        $query = "UPDATE " . $this->table_name . " SET Password = :Password, UserUpdatedAt = NOW() WHERE UserID = :UserID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':Password', $newPassword);
        $stmt->bindParam(':UserID', $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}

?>