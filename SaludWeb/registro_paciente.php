<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$error = null;
$success = null;
$pacientes_db = [];

try {
    require_once __DIR__ . '/db.php';

    // Cargar pacientes para el autocompletado
    $stmt = $pdo->query("SELECT * FROM pacientes WHERE activo = 1 ORDER BY nombre ASC");
    $pacientes_db = $stmt->fetchAll();
} catch (Exception $e) {
    $error = 'Error al acceder a la base de datos: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - SaludWEB</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; padding-top: 50px; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); width: 400px; }
        .selector-box { background: #e7f3ff; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #bde0fe; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
<div class="card">
    <h2 align="center">👤 Nuevo Registro</h2>
    <?php if (!empty($error)): ?>
        <div style="background:#fee2e2; color:#9b1c1c; padding:12px; border-radius:10px; margin-bottom:20px;">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div style="background:#d1fae5; color:#064e3b; padding:12px; border-radius:10px; margin-bottom:20px;">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <div class="selector-box">
        <label>Buscar Paciente Existente:</label>
        <select onchange="autoLlenar(this)">
            <option value="">-- Seleccionar --</option>
            <?php foreach ($pacientes_db as $p): ?>
                <option value="<?php echo $p['id']; ?>" data-dni="<?php echo $p['dni']; ?>" data-nom="<?php echo $p['nombre']; ?>" data-obra="<?php echo $p['id_obra_social']; ?>">
                    <?php echo $p['nombre']; ?> (<?php echo $p['dni']; ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <form action="procesar_registro.php" method="POST">
        <input type="text" name="dni" id="dn" placeholder="DNI" required>
        <input type="text" name="nombre" id="nom" placeholder="Nombre Completo" required>
        <select name="id_obra_social" id="obr">
            <option value="1">Particular</option>
            <option value="2">OSDE</option>
            <option value="3">PAMI</option>
        </select>
        <button type="submit">Guardar Paciente</button>
    </form>
    <p style="margin-top:18px; text-align:center;"><a href="/prog3-clase2/lista" style="color:#007bff; text-decoration:none;">← Volver al Dashboard</a></p>
</div>

<script>
function autoLlenar(sel) {
    const opt = sel.options[sel.selectedIndex];
    if(opt.value !== "") {
        document.getElementById('dn').value = opt.getAttribute('data-dni');
        document.getElementById('nom').value = opt.getAttribute('data-nom');
        document.getElementById('obr').value = opt.getAttribute('data-obra');
    }
}
</script>
</body>
</html>
