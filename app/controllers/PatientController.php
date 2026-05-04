<?php
class PatientController {
    private $db;
    private $patientModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        // Asumiendo que tienes un modelo Patient.php
        // $this->patientModel = new Patient($this->db);
    }

    public function index() {
        // Lógica para listar pacientes
        // $patients = $this->patientModel->all();
        require '../views/patients/index.php';
    }
}