<?php
class Patient {
    private $conn;
    private $table = 'patients';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtener todos los pacientes registrados
     */
    public function getAll() {
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtener un solo paciente por su ID
     */
    public function getById($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Registrar un nuevo paciente
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, dni, telefono, email, historial) 
                  VALUES (:nombre, :dni, :telefono, :email, :historial)";
        
        $stmt = $this->conn->prepare($query);

        // Sanitización de entradas
        $nombre    = htmlspecialchars(strip_tags($data['nombre'] ?? ''));
        $dni       = htmlspecialchars(strip_tags($data['dni'] ?? ''));
        $telefono  = htmlspecialchars(strip_tags($data['telefono'] ?? ''));
        $email     = htmlspecialchars(strip_tags($data['email'] ?? ''));
        $historial = htmlspecialchars(strip_tags($data['historial'] ?? ''));

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':historial', $historial);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Actualizar los datos de un paciente existente
     */
    public function update($data) {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, dni = :dni, telefono = :telefono, 
                      email = :email, historial = :historial 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Sanitización
        $id        = (int)$data['id'];
        $nombre    = htmlspecialchars(strip_tags($data['nombre'] ?? ''));
        $dni       = htmlspecialchars(strip_tags($data['dni'] ?? ''));
        $telefono  = htmlspecialchars(strip_tags($data['telefono'] ?? ''));
        $email     = htmlspecialchars(strip_tags($data['email'] ?? ''));
        $historial = htmlspecialchars(strip_tags($data['historial'] ?? ''));

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':historial', $historial);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Eliminar un paciente de la base de datos
     */
    public function delete($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Conteo total para el Dashboard
     */
    public function countAll() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row['total'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }
}