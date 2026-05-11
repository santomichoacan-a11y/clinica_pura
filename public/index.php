<?php
// public/index.php

// 1. CARGA LA CONFIGURACIÓN GLOBAL PRIMERO
require_once __DIR__ . '/../app/config/app.php'; 

require_once '../app/config/db.php';
require_once '../app/controllers/AuthController.php';

$database = new Database();
$db = $database->getConnection();

// Lógica para capturar la acción desde la URL amigable
$base_path = '/clinica_pura/public/';
$request_uri = $_SERVER['REQUEST_URI'];
$path = str_replace($base_path, '', $request_uri);
$parts = explode('?', $path);
$action = trim($parts[0], '/');

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
        checkAuth();
        require_once '../app/models/Patient.php';
        require_once '../app/models/Consultation.php'; // Nuevo Modelo
        
        $pModel = new Patient($db);
        $cModel = new Consultation($db);
        
        $totalPacientes = $pModel->countAll(); 
        $totalConsultas = $cModel->countTotal(); // Para tu nuevo contador

        include '../view/dashboard/index.php'; 
        break;

    case 'usuarios':
        checkAuth();
        require_once __DIR__ . '/../app/controllers/users_controller.php';
        break;

    /* --- MÓDULO DE PACIENTES --- */
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

    case 'consultas':
        checkAuth();
        require_once '../app/controllers/ConsultationsController.php'; // 1. Cargas el archivo
        $consultationCtrl = new ConsultationsController($db);         // 2. Creas el objeto
        $consultationCtrl->index();                                   // 3. Llamas al método
        break;

    case 'consultas/guardar':
        checkAuth();
        require_once '../app/controllers/ConsultationsController.php';
        $consultationCtrl = new ConsultationsController($db);
        $consultationCtrl->save();
        break;

    case 'consultas/eliminar':
        checkAuth();
        require_once '../app/controllers/ConsultationsController.php';
        $consultationCtrl = new ConsultationsController($db);
        $id = $_GET['id'] ?? null;
        $consultationCtrl->eliminar($id);
        break;
        
    case 'logout':
        require_once __DIR__ . '/../app/controllers/logout.php';
        break;

    default:
        if ($action !== 'login') {
            header("Location: " . BASE_URL . "login");
            exit();
        }
        include '../view/auth/login.php';
        break;
}