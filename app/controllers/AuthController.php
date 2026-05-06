<?php
class AuthController {
    private $db;
    private $userModel;

    public function __construct($db) {
        $this->db = $db;
        require_once '../app/models/User.php';
        $this->userModel = new User($this->db);
    }

    public function login() {
        // En app.php ya se inicia la sesión, pero dejamos esto como doble seguro
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // BLOQUEO 1: Si ya está logueado y trata de forzar la vista de login, al dashboard
        if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "dashboard");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']); // Limpiamos espacios en blanco accidentales
            $password = $_POST['password'];

            $user = $this->userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                // Regenerar el ID de sesión por seguridad tras el login exitoso
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Redirección absoluta y segura usando tu constante global
                header("Location: " . BASE_URL . "dashboard");
                exit();
            } else {
                // Pasamos el error a la vista de login de forma elegante
                $error = "Usuario o contraseña incorrectos";
                include '../view/auth/login.php';
            }
        } else {
            // Si es una petición GET normal, mostramos el login
            include '../view/auth/login.php';
        }
    }
}