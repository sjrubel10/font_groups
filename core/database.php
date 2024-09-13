<?php

class database
{
    private $servername = "localhost";
    private $username = "root";
    private $password = ""; // Empty password
    private $dbname = "font_group";
    public $conn;

    public function __construct() {
        // Create a new mysqli instance
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to close the database connection
    public function close_connection() {
        if ( $this->conn ) {
            $this->conn->close();
        }
    }
}