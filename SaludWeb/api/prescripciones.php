<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de prescripción inválido.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('DELETE FROM prescripciones WHERE id = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Prescripción no encontrada.']);
            exit;
        }
        echo json_encode(['message' => 'Prescripción eliminada correctamente.']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al eliminar la prescripción: ' . $e->getMessage()]);
    }
    exit;
}

try {
    $estado = $_GET['estado'] ?? 'activa';
    $stmt = $pdo->prepare('SELECT p.*, pac.nombre AS paciente_nombre, m.nombre AS medico_nombre FROM prescripciones p LEFT JOIN pacientes pac ON p.id_paciente = pac.id LEFT JOIN medicos m ON p.id_medico = m.id WHERE p.estado = ? ORDER BY p.fecha_emision DESC');
    $stmt->execute([$estado]);
    echo json_encode(['data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}
