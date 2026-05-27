<?php
require_once __DIR__ . '/../../config/database.php';

class MdUsuarios {

    public static function login($usuario, $password) {

        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT u.*, e.nombre as empresa
            FROM usuarios u
            INNER JOIN empresas e ON e.id = u.empresa_id
            WHERE u.usuario = :usuario
            AND u.password = :password
            LIMIT 1
        ");

        $stmt->bindParam(":usuario", $usuario);
        $stmt->bindParam(":password", $password);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>