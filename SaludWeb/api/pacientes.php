<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
try {
    if ($id) {
        $stmt = $pdo->prepare('SELECT * FROM pacientes WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            http_response_code(404);
            echo json_encode(['error' => 'Paciente no encontrado.']);
            exit;
        }
        echo json_encode(['data' => $data]);
        exit;
    }

    $stmt = $pdo->query('SELECT * FROM pacientes ORDER BY activo DESC, nombre ASC');
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['data' => $data]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}
