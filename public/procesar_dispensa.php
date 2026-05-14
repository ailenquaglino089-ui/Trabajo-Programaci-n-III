<?php
// SaludWeb/procesar_dispensa.php
require_once __DIR__ . '/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Actualizamos el estado para "quemar" la receta
        $stmt = $pdo->prepare("UPDATE prescripciones SET estado = 'dispensada' WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: farmacia?mensaje=Medicamento entregado correctamente. La receta ha sido archivada.");
        exit();
    } catch (Exception $e) {
        die("Error al procesar la entrega: " . $e->getMessage());
    }
}