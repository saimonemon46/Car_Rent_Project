<?php
// class Session {
//     public static function init() {
//         if (session_status() == PHP_SESSION_NONE) {
//             session_start();
//         }
//     }

//     public static function set($key, $val) {
//         $_SESSION[$key] = $val;
//     }

//     public static function get($key) {
//         return $_SESSION[$key] ?? null;
//     }

//     public static function isLoggedIn() {
//         return isset($_SESSION['admin_id']);
//     }

//     public static function logout() {
//         session_unset();
//         session_destroy();
//     }
// }