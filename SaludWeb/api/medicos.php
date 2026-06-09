<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true);
    $activo = isset($input['activo']) ? (int) $input['activo'] : null;

    if (!$id || !in_array($activo, [0, 1], true)) {
        respond_error('ID de médico o estado inválido.', 400);
    }

    try {
        $stmt = $pdo->prepare('UPDATE medicos SET activo = ? WHERE id = ?');
        $stmt->execute([$activo, $id]);
        respond_ok(['message' => 'Estado de médico actualizado con éxito.']);
    } catch (Exception $e) {
        respond_error('Error al actualizar médico: ' . $e->getMessage(), 500);
    }
}

try {
    $stmt = $pdo->query('SELECT * FROM medicos ORDER BY activo DESC, nombre ASC');
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    respond_ok($data);
} catch (Exception $e) {
    respond_error('Error interno: ' . $e->getMessage(), 500);
}
