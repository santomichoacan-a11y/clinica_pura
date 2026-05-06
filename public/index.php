<?php
// public/index.php

// 1. CARGA LA CONFIGURACIÓN GLOBAL PRIMERO
// Esto activa el Anti-Caché del botón atrás, inicia la sesión de forma segura
// y te da acceso a las constantes BASE_URL y VIEW_PATH.
require_once __DIR__ . '/../app/config/app.php'; 

require_once '../app/config/db.php';
require_once '../app/controllers/AuthController.php';

$database = new Database();
$db = $database->getConnection();

// Lógica para capturar la acción desde la URL amigable
$base_path = '/clinica_pura/public/';
$request_uri = $_SERVER['REQUEST_URI'];
$path = str_replace($base_path, '', $request_uri);
$action = explode('?', $path)[0];
$action = trim($action, '/');

// Acción por defecto
if ($action == '' || $action == 'index.php') {
    $action = 'login';
}

switch ($action) {
    case 'login':
        $auth = new AuthController($db);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $auth->login();
        } else {
            include '../view/auth/login.php';
        }
        break;

    case 'dashboard':
        // Usamos la función global que definiste en app.php
        checkAuth();
        
        // Cargar datos dinámicos para las estadísticas del dashboard
        require_once '../app/models/Patient.php';
        $pModel = new Patient($db);
        $totalPacientes = $pModel->countAll(); 

        include '../view/dashboard/index.php'; 
        break;

    case 'usuarios':
        checkAuth();
        // Interceptamos la URL limpia y cargamos el controlador que creamos
        require_once __DIR__ . '/../app/controllers/users_controller.php';
        break;

    case 'pacientes':
        checkAuth();
        require_once '../app/controllers/PatientController.php';
        $patientCtrl = new PatientController($db);
        $patientCtrl->index();
        break;

    case 'pacientes/store':
        checkAuth();
        require_once '../app/controllers/PatientController.php';
        $patientCtrl = new PatientController($db);
        $patientCtrl->store();
        break;

    case 'pacientes/editar':
        checkAuth();
        require_once '../app/controllers/PatientController.php';
        $id = $_GET['id'] ?? null;
        if ($id) {
            $patientCtrl = new PatientController($db);
            $patientCtrl->edit($id);
        } else {
            header("Location: " . BASE_URL . "pacientes");
            exit();
        }
        break;

    case 'pacientes/update':
        checkAuth();
        require_once '../app/controllers/PatientController.php';
        $patientCtrl = new PatientController($db);
        $patientCtrl->update();
        break;

    case 'pacientes/delete':
        checkAuth();
        require_once '../app/controllers/PatientController.php';
        $id = $_GET['id'] ?? null;
        if ($id) {
            $patientCtrl = new PatientController($db);
            $patientCtrl->destroy($id);
        } else {
            header("Location: " . BASE_URL . "pacientes");
            exit();
        }
        break;

    // MODIFICADO: Ahora llamamos a tu script de destrucción masiva y segura
    case 'logout':
        require_once __DIR__ . '/../app/controllers/logout.php';
        break;

    default:
        // Evita el bucle: si ya estás en login, no redirijas de nuevo a login
        if ($action !== 'login') {
            header("Location: " . BASE_URL . "login");
            exit();
        }
        include '../view/auth/login.php';
        break;
}