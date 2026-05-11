<?php
require_once __DIR__ . '/../models/Consultation.php';
require_once __DIR__ . '/../models/Patient.php';

class ConsultationsController {
    private $model;
    private $patientModel;

    public function __construct($db) {
        $this->model = new Consultation($db);
        $this->patientModel = new Patient($db);
    }

    public function index() {
        $consultations = $this->model->getAll();
        $patients = $this->patientModel->getAll();
        $activePage = 'consultas'; 
        include '../view/consultations/index.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Capturamos los datos incluyendo 'treatment' que está en tu tabla
            $data = [
                'id'         => $_POST['id'] ?? null,
                'patient_id' => $_POST['patient_id'] ?? '',
                'reason'     => htmlspecialchars(trim($_POST['reason'])),
                'diagnosis'  => htmlspecialchars(trim($_POST['diagnosis'] ?? '')),
                'treatment'  => htmlspecialchars(trim($_POST['treatment'] ?? ''))
            ];

            if (!empty($data['id'])) {
                // Si hay ID, actualizamos el registro existente
                $this->model->update($data);
                $msg = 'updated=1';
            } else {
                // Si no hay ID, creamos uno nuevo
                $this->model->create($data);
                $msg = 'success=1';
            }
            
            // REDIRECCIÓN AMIGABLE: Evita el uso de index.php?action=
            // Esto asegura que el switch de tu index.php capture 'consultas' correctamente
            header('Location: ' . BASE_URL . 'consultas?' . $msg);
            exit();
        }
    }

    public function eliminar($id) {
        if ($id && is_numeric($id)) {
            $this->model->delete($id);
            // REDIRECCIÓN AMIGABLE
            header('Location: ' . BASE_URL . 'consultas?deleted=1');
            exit();
        }
        // En caso de error, volvemos a la lista principal
        header('Location: ' . BASE_URL . 'consultas');
        exit();
    }
}