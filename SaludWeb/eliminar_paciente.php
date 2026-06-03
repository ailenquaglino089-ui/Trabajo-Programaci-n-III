<?php
// SaludWEB/eliminar_paciente.php

if (session_status() === PHP_SESSION_NONE) { session_start(); }

try {
    require_once __DIR__ . '/db.php';

    $id = $_GET['id'] ?? null;

    if ($id) {
        // Implementación de Borrado Lógico (Soft Delete):
        // No eliminamos la fila (DELETE), sino que la marcamos como inactiva.
        $stmt = $pdo->prepare("UPDATE pacientes SET activo = 0 WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            // 5. CREAMOS EL MENSAJE PARA LA NOTIFICACIÓN
            // Usamos la palabra "eliminado" para que el dashboard lo pinte de rojo
            $_SESSION['mensaje'] = "El paciente ha sido eliminado correctamente y enviado a la papelera.";
        } else {
            $_SESSION['mensaje'] = "Error: No se pudo eliminar el paciente.";
            $_SESSION['tipo_mensaje'] = "danger";
        }
    } else {
        $_SESSION['mensaje'] = "Error: ID de paciente no válido.";
        $_SESSION['tipo_mensaje'] = "danger";
    }

} catch (PDOException $e) {
    $_SESSION['mensaje'] = "Error de base de datos: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "danger";
}

// 6. Volvemos automáticamente a la lista
header("Location: lista");
exit();
?>
