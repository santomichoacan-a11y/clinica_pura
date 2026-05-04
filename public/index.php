<?php
require_once '../app/config/db.php';
require_once '../app/controllers/AuthController.php';

$url = $_SERVER['REQUEST_URI'];

// Enrutador sencillo
switch ($url) {
    case '/':
    case '/login':
        $auth = new AuthController();
        $auth->login();
        break;
        
    case '/dashboard':
        require '../views/dashboard/index.php';
        break;

    case '/logout':
        session_start();
        session_destroy();
        header('Location: /login');
        break;

    default:
        http_response_code(404);
        echo "Página no encontrada";
        break;
}