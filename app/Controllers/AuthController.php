<?php
// class AuthController extends Controller {
//     public function login() {
//         if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//             $email = trim($_POST['email']);
//             $password = trim($_POST['password']);

//             $adminModel = new Admin();
//             $user = $adminModel->findByEmail($email);

//             if ($user && password_verify($password, $user->password)) {
//                 Session::set('admin_id', $user->id);
//                 Session::set('admin_name', $user->username);
//                 header('Location: ' . URLROOT . '/dashboard');
//             } else {
//                 // Return to view with error
//                 $this->view('admin/login', ['error' => 'Invalid credentials']);
//             }
//         } else {
//             $this->view('admin/login');
//         }
//     }




    
// }