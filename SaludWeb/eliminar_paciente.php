<?php
// 1. Iniciamos sesión para que el mensaje viaje a la otra página
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 2. Conexión a la base de datos 'pacientes'
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pacientes;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. Obtenemos el ID desde la URL
    $id = $_GET['id'] ?? null;

    if ($id) {
        // 4. Ejecutamos el borrado lógico (pasamos activo a 0)
        $stmt = $pdo->prepare("UPDATE pacientes SET activo = 0 WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            // 5. CREAMOS EL MENSAJE PARA LA NOTIFICACIÓN
            // Usamos la palabra "eliminado" para que el dashboard lo pinte de rojo
            $_SESSION['mensaje'] = "El paciente ha sido eliminado correctamente y enviado a la papelera.";
        } else {
            $_SESSION['mensaje'] = "Error: No se pudo eliminar el paciente.";
        }
    } else {
        $_SESSION['mensaje'] = "Error: ID de paciente no válido.";
    }

} catch (PDOException $e) {
    $_SESSION['mensaje'] = "Error de base de datos: " . $e->getMessage();
}

// 6. Volvemos automáticamente a la lista
header("Location: lista");
exit();
?>
