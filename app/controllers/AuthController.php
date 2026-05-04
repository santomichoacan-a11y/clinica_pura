<?php
class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $_POST['username'];
            $pass = $_POST['password'];

            // 1. Buscar usuario en Postgres (PDO)
            // $userData = User::findByUsername($user);

            if ($userData && password_verify($pass, $userData['password'])) {
                session_start();
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['role'] = $userData['role']; // 'admin' o 'doctor'

                header('Location: /dashboard');
            }
        }
        require '../views/auth/login.php';
    }

    public function changePassword() {
        // Solo permitir si hay sesión
        // UPDATE users SET password = ... WHERE id = ...
    }
}