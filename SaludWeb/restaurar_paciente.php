<?php
// SaludWEB/restaurar_paciente.php

// Iniciar sesión para manejar mensajes de retroalimentación (Flash Messages)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// Validar que el ID recibido sea numérico antes de operar
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Reversión del borrado lógico: Cambiamos el estado 'activo' a 1
        // Esto permite recuperar datos sin haber eliminado físicamente la fila de la DB
        $sql = "UPDATE pacientes SET activo = 1 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        $_SESSION['mensaje'] = "Paciente restaurado con éxito";
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al restaurar: " . $e->getMessage();
    }
}

// Redirección automática al listado principal después de la operación
header("Location: lista");
exit();
