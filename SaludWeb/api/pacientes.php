<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$showAll = isset($_GET['all']) && $_GET['all'] === '1';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        if (!$id) {
            respond_error('ID de paciente inválido.', 400);
        }

        $stmt = $pdo->prepare('SELECT id FROM pacientes WHERE id = ? AND activo = 1');
        $stmt->execute([$id]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            respond_error('Paciente no encontrado o ya eliminado.', 404);
        }

        $stmt = $pdo->prepare('UPDATE pacientes SET activo = 0 WHERE id = ?');
        $stmt->execute([$id]);

        respond_ok(['message' => 'Paciente eliminado lógicamente.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        if (!$id) {
            respond_error('ID de paciente inválido.', 400);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            respond_error('JSON inválido.', 400);
        }

        $errors = [];
        $fields = [];
        $params = [];

        if (array_key_exists('nombre', $input)) {
            $nombre = trim($input['nombre']);
            if ($nombre === '' || mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
                $errors[] = 'El nombre debe tener entre 3 y 100 caracteres.';
            } else {
                $fields[] = 'nombre = ?';
                $params[] = $nombre;
            }
        }

        if (array_key_exists('dni', $input)) {
            $dni = trim($input['dni']);
            if ($dni === '') {
                $fields[] = 'dni = NULL';
            } else {
                if (!preg_match('/^[0-9A-Za-z\- ]{1,50}$/u', $dni)) {
                    $errors[] = 'El DNI tiene un formato inválido.';
                } else {
                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM pacientes WHERE dni = ? AND id != ?');
                    $stmt->execute([$dni, $id]);
                    if ($stmt->fetchColumn() > 0) {
                        $errors[] = 'El DNI ya está en uso por otro paciente.';
                    } else {
                        $fields[] = 'dni = ?';
                        $params[] = $dni;
                    }
                }
            }
        }

        if (array_key_exists('id_obra_social', $input)) {
            $idObra = (int) $input['id_obra_social'];
            if ($idObra <= 0) {
                $errors[] = 'La obra social es obligatoria.';
            } else {
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM obras_sociales WHERE id = ?');
                $stmt->execute([$idObra]);
                if ($stmt->fetchColumn() === 0) {
                    $errors[] = 'La obra social no existe.';
                } else {
                    $fields[] = 'id_obra_social = ?';
                    $params[] = $idObra;
                }
            }
        }

        if (empty($fields)) {
            respond_error('No se recibieron campos válidos para actualizar.', 400);
        }
        if (!empty($errors)) {
            respond_error($errors, 400);
        }

        $stmt = $pdo->prepare('SELECT id FROM pacientes WHERE id = ? AND activo = 1');
        $stmt->execute([$id]);
        if (!$stmt->fetchColumn()) {
            respond_error('Paciente no encontrado o inactivo.', 404);
        }

        $params[] = $id;
        $stmt = $pdo->prepare('UPDATE pacientes SET ' . implode(', ', $fields) . ' WHERE id = ?');
        $stmt->execute($params);

        $stmt = $pdo->prepare('SELECT * FROM pacientes WHERE id = ?');
        $stmt->execute([$id]);
        respond_ok($stmt->fetch(PDO::FETCH_ASSOC));
    }

    if ($id) {
        if ($showAll) {
            $stmt = $pdo->prepare('SELECT * FROM pacientes WHERE id = ?');
        } else {
            $stmt = $pdo->prepare('SELECT * FROM pacientes WHERE id = ? AND activo = 1');
        }
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            respond_error('Paciente no encontrado.', 404);
        }

        respond_ok($data);
    }

    if ($showAll) {
        $stmt = $pdo->query('SELECT * FROM pacientes ORDER BY activo DESC, nombre ASC');
    } else {
        $stmt = $pdo->query('SELECT * FROM pacientes WHERE activo = 1 ORDER BY nombre ASC');
    }
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    respond_ok($data);
} catch (Exception $e) {
    respond_error('Error interno: ' . $e->getMessage(), 500);
}
