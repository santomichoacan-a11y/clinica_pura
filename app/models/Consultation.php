<?php
class Consultation {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function create($data) {
        $sql = "INSERT INTO consultations (patient_id, user_id, reason, diagnosis, treatment, created_at) 
                VALUES (:patient_id, :user_id, :reason, :diagnosis, :treatment, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':patient_id' => $data['patient_id'],
            ':user_id'    => $_SESSION['user_id'] ?? 1,
            ':reason'     => $data['reason'],
            // Usamos null coalescing para evitar errores si el índice no existe
            ':diagnosis'  => $data['diagnosis'] ?? null,
            ':treatment'  => $data['treatment'] ?? null 
        ]);
    }

    public function getAll() {
        // Optimización: Seleccionamos solo lo necesario para no sobrecargar la memoria
        $sql = "SELECT c.id, c.patient_id, c.reason, c.diagnosis, c.created_at, p.nombre as paciente_nombre 
                FROM consultations c 
                JOIN patients p ON c.patient_id = p.id 
                ORDER BY c.created_at DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($data) {
        $sql = "UPDATE consultations SET 
                patient_id = :patient_id, 
                reason = :reason, 
                diagnosis = :diagnosis, 
                treatment = :treatment 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'         => $data['id'],
            ':patient_id' => $data['patient_id'],
            ':reason'     => $data['reason'],
            ':diagnosis'  => $data['diagnosis'] ?? null,
            ':treatment'  => $data['treatment'] ?? null
        ]);
    }

    public function delete($id) {
        // Doble validación: nos aseguramos de que el ID sea entero
        $sql = "DELETE FROM consultations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function countTotal() {
        return $this->db->query("SELECT COUNT(*) FROM consultations")->fetchColumn();
    }
}