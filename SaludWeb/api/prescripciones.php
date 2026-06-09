<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!$id) {
        respond_error('ID de prescripción inválido.', 400);
    }

    try {
        $stmt = $pdo->prepare('DELETE FROM prescripciones WHERE id = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) {
            respond_error('Prescripción no encontrada.', 404);
        }
        respond_ok(['message' => 'Prescripción eliminada correctamente.']);
    } catch (Exception $e) {
        respond_error('Error al eliminar la prescripción: ' . $e->getMessage(), 500);
    }
}

try {
    $estado = $_GET['estado'] ?? 'activa';
    $stmt = $pdo->prepare('SELECT p.*, pac.nombre AS paciente_nombre, m.nombre AS medico_nombre FROM prescripciones p LEFT JOIN pacientes pac ON p.id_paciente = pac.id LEFT JOIN medicos m ON p.id_medico = m.id WHERE p.estado = ? ORDER BY p.fecha_emision DESC');
    $stmt->execute([$estado]);
    respond_ok($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    respond_error('Error interno: ' . $e->getMessage(), 500);
}
