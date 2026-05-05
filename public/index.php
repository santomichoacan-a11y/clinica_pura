<?php
// public/index.php
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
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Seguridad: Si no hay sesión, volver al login
        if (!isset($_SESSION['user_id'])) {
            header("Location: login");
            exit();
        }
        
        // Cargar datos dinámicos para las estadísticas del dashboard
        require_once '../app/models/Patient.php';
        $pModel = new Patient($db);
        $totalPacientes = $pModel->countAll(); 

        include '../view/dashboard/index.php'; 
        break;

        case 'usuarios':
        // Interceptamos la URL limpia y cargamos el controlador que creamos
        require_once __DIR__ . '/../app/controllers/users_controller.php';
        break;

    case 'pacientes':
        require_once '../app/controllers/PatientController.php';
        $patientCtrl = new PatientController($db);
        $patientCtrl->index();
        break;

    case 'pacientes/store':
        require_once '../app/controllers/PatientController.php';
        $patientCtrl = new PatientController($db);
        $patientCtrl->store();
        break;

    // --- NUEVAS RUTAS AGREGADAS PARA CORREGIR EL ERROR ---

    case 'pacientes/editar':
        require_once '../app/controllers/PatientController.php';
        $id = $_GET['id'] ?? null;
        if ($id) {
            $patientCtrl = new PatientController($db);
            $patientCtrl->edit($id);
        } else {
            header("Location: ../pacientes");
        }
        break;

    case 'pacientes/update':
        require_once '../app/controllers/PatientController.php';
        $patientCtrl = new PatientController($db);
        $patientCtrl->update();
        break;

    case 'pacientes/delete':
        require_once '../app/controllers/PatientController.php';
        $id = $_GET['id'] ?? null;
        if ($id) {
            $patientCtrl = new PatientController($db);
            $patientCtrl->destroy($id);
        } else {
            header("Location: ../pacientes");
        }
        break;

    // ---------------------------------------------------

    case 'logout':
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: login");
        exit();
        break;

    default:
        // Evita el bucle: si ya estás en login, no redirijas de nuevo a login
        if ($action !== 'login') {
            header("Location: login");
            exit();
        }
        include '../view/auth/login.php';
        break;
}