<?php
// SaludWEB/restaurar_paciente.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Cambiamos activo a 1 para que lista_pacientes.php lo vuelva a mostrar
        $sql = "UPDATE pacientes SET activo = 1 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        $_SESSION['mensaje'] = "Paciente restaurado con éxito";
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al restaurar: " . $e->getMessage();
    }
}

header("Location: lista");
exit();
