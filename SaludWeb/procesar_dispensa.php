<?php
// SaludWeb/procesar_dispensa.php
require_once __DIR__ . '/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // Cambiamos el estado a dispensada para que no aparezca más en la farmacia
        $stmt = $pdo->prepare("UPDATE prescripciones SET estado = 'dispensada' WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: farmacia?exito=1");
        exit();
    } catch (Exception $e) {
        die("Error crítico al procesar: " . $e->getMessage());
    }
}