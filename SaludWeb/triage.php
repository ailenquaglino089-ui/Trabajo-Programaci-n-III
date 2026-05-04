<?php
require_once __DIR__ . '/db.php';;

// Si venís del dashboard, capturamos el ID para saber de quién hablamos
$nombre_seleccionado = "";
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT nombre FROM pacientes WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $p = $stmt->fetch();
    $nombre_seleccionado = $p['nombre'] ?? "";
}

// Lista para el buscador inteligente
$todos = $pdo->query("SELECT nombre FROM pacientes WHERE activo = 1 ORDER BY nombre ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cargar Triage</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; padding: 50px; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 400px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2>📋 Cargar Triage</h2>
        <form action="/prog3-clase2/guardar_triage" method="POST">
            <label>Paciente:</label>
            <input list="nombres" name="nombre_paciente" value="<?= htmlspecialchars($nombre_seleccionado) ?>" placeholder="Escribí o seleccioná..." required>
            <datalist id="nombres">
                <?php foreach($todos as $t): ?>
                    <option value="<?= htmlspecialchars($t['nombre']) ?>">
                <?php endforeach; ?>
            </datalist>

            <label>Nivel de Urgencia:</label>
            <select name="nivel_gravedad" required>
                <option value="10">10 - Emergencia Total</option>
                <option value="8">8 - Muy Urgente</option>
                <option value="5">5 - Urgente (Riesgo)</option>
                <option value="3">3 - Estable</option>
            </select>

            <label>Notas Médicas:</label>
            <textarea name="observaciones" rows="3"></textarea>

            <button type="submit">Guardar y Actualizar Lista</button>
        </form>
    </div>
</body>
</html>
