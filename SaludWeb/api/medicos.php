<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true);
    $activo = isset($input['activo']) ? (int) $input['activo'] : null;

    if (!$id || !in_array($activo, [0,1], true)) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de médico o estado inválido.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('UPDATE medicos SET activo = ? WHERE id = ?');
        $stmt->execute([$activo, $id]);
        echo json_encode(['message' => 'Estado de médico actualizado con éxito.']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar médico: ' . $e->getMessage()]);
    }
    exit;
}

try {
    $stmt = $pdo->query('SELECT * FROM medicos ORDER BY activo DESC, nombre ASC');
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['data' => $data]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}
