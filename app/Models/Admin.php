<?php
// class Admin {
//     private $db;

//     public function __construct() {
//         $this->db = new Database(); 
//     }

//     public function findByEmail($email) {
//         $this->db->query("SELECT * FROM admins WHERE email = ? LIMIT 1");
//         // 's' means the parameter is a string
//         $this->db->bind('s', $email);
//         return $this->db->single();
//     }

//     public function updatePassword($id, $hashedPassword) {
//         $this->db->query("UPDATE admins SET password = ? WHERE id = ?");
//         // 'si' means first param is string (hash), second is integer (id)
//         $this->db->bind('si', $hashedPassword, $id);
//         return $this->db->execute();
//     }

//     public function updateProfile($id, $username, $email) {
//         $this->db->query("UPDATE admins SET username = ?, email = ? WHERE id = ?");
//         $this->db->bind('ssi', $username, $email, $id);
//         return $this->db->execute();
//     }
// }