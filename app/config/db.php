<?php
class Database {
    private $host = "127.0.0.1";
    private $db_name = "clinica_db"; // Asegúrate de que se llame así en phpMyAdmin
    private $username = "root";      // Usuario por defecto en XAMPP
    private $password = "";          // Contraseña por defecto en XAMPP
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Cambiamos "pgsql" por "mysql" y quitamos el puerto si es el estándar (3306)
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}