<?php
/**
 * Controlador para la gestión de Pacientes
 */
class PatientController {
    private $db;
    private $patientModel;

    public function __construct($db) {
        $this->db = $db;
        require_once '../app/models/Patient.php';
        $this->patientModel = new Patient($this->db);

        // Iniciamos sesión globalmente para evitar bucles por falta de persistencia
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Validador privado para evitar repetición de código y bucles
     */
    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) { 
            // Usamos una ruta absoluta desde la raíz para evitar errores de ../
            header("Location: /clinica_pura/public/login"); 
            exit(); 
        }
    }

    /**
     * Muestra la lista de pacientes
     */
    public function index() {
        $this->checkAuth();
        $patients = $this->patientModel->getAll();
        require '../view/patients/index.php';
    }

    /**
     * Procesa el registro de un nuevo paciente
     */
    public function store() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre'    => trim($_POST['nombre'] ?? ''),
                'dni'       => trim($_POST['dni'] ?? ''),
                'telefono'  => trim($_POST['telefono'] ?? ''),
                'email'     => trim($_POST['email'] ?? ''),
                'historial' => trim($_POST['historial'] ?? '')
            ];

            if (empty($data['nombre']) || empty($data['dni'])) {
                header("Location: ../pacientes?status=error&msg=campos_vacios");
                exit();
            }

            if ($this->patientModel->create($data)) {
                header("Location: ../pacientes?status=success");
            } else {
                header("Location: ../pacientes?status=error");
            }
            exit();
        }
    }

    /**
     * Muestra el formulario de edición
     */
    public function edit($id) {
        $this->checkAuth();
        $patient = $this->patientModel->getById($id);

        if (!$patient) {
            header("Location: ../pacientes?status=error&msg=no_encontrado");
            exit();
        }

        require '../view/patients/edit.php';
    }

    /**
     * Procesa la actualización
     */
    public function update() {
    $this->checkAuth(); // Verifica que el usuario esté logueado
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'id'        => $_POST['id'],
            'nombre'    => trim($_POST['nombre'] ?? ''),
            'dni'       => trim($_POST['dni'] ?? ''),
            'telefono'  => trim($_POST['telefono'] ?? ''),
            'email'     => trim($_POST['email'] ?? ''),
            'historial' => trim($_POST['historial'] ?? '')
        ];

        if ($this->patientModel->update($data)) {
            // Redirige a la lista principal con mensaje de éxito
            header("Location: ../pacientes?status=updated");
        } else {
            header("Location: ../pacientes?status=error");
        }
        exit(); 
    }
}

    /**
     * Elimina un paciente
     */
    public function destroy($id) {
        $this->checkAuth();
        
        if ($id && $this->patientModel->delete($id)) {
            // Si el borrado es exitoso, volvemos a la lista
            header("Location: ../pacientes?status=deleted");
        } else {
            header("Location: ../pacientes?status=error");
        }
        exit();
    }
}