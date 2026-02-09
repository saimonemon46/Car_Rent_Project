<?php
// class Database {
//     private $host = DB_HOST;
//     private $user = DB_USER;
//     private $pass = DB_PASS;
//     private $dbname = DB_NAME;

//     private $connection;
//     private $stmt;

//     public function __construct() {
//         $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

//         if ($this->connection->connect_error) {
//             die("Connection failed: " . $this->connection->connect_error);
//         }
//     }

//     // Prepare statement
//     public function query($sql) {
//         $this->stmt = $this->connection->prepare($sql);
//     }

//     // Bind values (MySQLi needs type strings like 's' for string, 'i' for int)
//     public function bind($types, ...$params) {
//         $this->stmt->bind_param($types, ...$params);
//     }

//     // Execute and return result set (for SELECT)
//     public function resultSet() {
//         $this->stmt->execute();
//         return $this->stmt->get_result();
//     }

//     // Execute and return a single row
//     public function single() {
//         $this->stmt->execute();
//         $result = $this->stmt->get_result();
//         return $result->fetch_object(); // Returns as an object
//     }

//     // Execute for INSERT/UPDATE/DELETE
//     public function execute() {
//         return $this->stmt->execute();
//     }
// }