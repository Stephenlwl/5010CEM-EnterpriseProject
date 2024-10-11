<?php

class Admin {

    // Database connection and table name
    private $conn;
    private $table_name = "admin";
    // Object properties
    public $AdminID;
    public $AdminName;
    public $Email;
    public $AdminPassword;

    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create user
    function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                  SET AdminName=:AdminName, Email=:Email, AdminPassword=:AdminPassword";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->AdminName = htmlspecialchars(strip_tags($this->AdminName));
        $this->Email = htmlspecialchars(strip_tags($this->Email));
        $this->AdminPassword = htmlspecialchars(strip_tags($this->AdminPassword));

        // Bind values
        $stmt->bindParam(":AdminName", $this->AdminName);
        $stmt->bindParam(":Email", $this->Email);
        $stmt->bindParam(":AdminPassword", $this->AdminPassword);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);

        return false;
    }
    
    public function checkCredentials($email, $password) {
        $query = "SELECT AdminID, AdminName, AdminPassword FROM " . $this->table_name . " WHERE Email = :Email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':Email', $email);
        $stmt->execute();
    
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Debugging: Check the content of $admin
        error_log(print_r($admin, true)); // Log the admin details for debugging
        
        // Check if email exists and password matches
        if ($admin && password_verify($password, $admin['AdminPassword'])) {
            return [
                'AdminID' => $admin['AdminID'],
                'AdminName' => $admin['AdminName'],
            ]; 
        } else {
            return false; 
        }
    }    
    
}

?>