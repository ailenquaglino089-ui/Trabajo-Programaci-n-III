<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_paciente'];
    $gravedad = $_POST['nivel_gravedad'];
    $obs = $_POST['observaciones'];

    try {
        // Buscamos al paciente (Simona, Homero, etc.)
        $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE nombre = ?");
        $stmt->execute([$nombre]);
        $paciente = $stmt->fetch();

        if ($paciente) {
            $id_p = $paciente['id'];
        } else {
            $ins = $pdo->prepare("INSERT INTO pacientes (nombre, activo) VALUES (?, 1)");
            $ins->execute([$nombre]);
            $id_p = $pdo->lastInsertId();
        }

        // Insertamos el Triage usando los nombres de tu tabla (id_paciente, nivel_gravedad, observaciones, fecha)
        $sql = "INSERT INTO triages (id_paciente, nivel_gravedad, observaciones, fecha) VALUES (?, ?, ?, NOW())";
        $pdo->prepare($sql)->execute([$id_p, $gravedad, $obs]);

        header("Location: lista?mensaje=Triage guardado correctamente");
    } catch (Exception $e) {
        die("Error al guardar: " . $e->getMessage());
    }
} else {
    header("Location: triage");
    exit();
}
