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
        // Iniciar sesión para verificar si ya existe una
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // BLOQUEO 1: Si ya está logueado y trata de ver el login, al dashboard
        if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: dashboard");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                // Regenerar el ID de sesión por seguridad tras el login
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                header("Location: dashboard");
                exit();
            } else {
                // Puedes pasar un error a la vista en lugar de un echo
                $error = "Usuario o contraseña incorrectos";
                include '../view/auth/login.php';
            }
        } else {
            // Si es una petición GET normal, mostramos el login
            include '../view/auth/login.php';
        }
    }
}