<?php
require_once __DIR__ . '/db.php';

$id = $_GET['id'] ?? null;
if (!$id) { die("ID no recibido."); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "UPDATE pacientes SET nombre = ?, dni = ?, id_obra_social = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['nombre'], $_POST['dni'], $_POST['id_obra_social'], $id]);
    header("Location: lista_pacientes.php?mensaje=Actualizado");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { die("Paciente no encontrado en la base de datos."); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Estrella</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 320px; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn { background: #007bff; color: white; border: none; width: 100%; padding: 12px; border-radius: 5px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="color:#007bff; text-align:center;">✏️ Editar Perfil</h2>
        <form method="POST">
            <label>Nombre</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($p['nombre']) ?>" required>
            <label>DNI</label>
            <input type="text" name="dni" value="<?= htmlspecialchars($p['dni']) ?>" required>
            <label>Obra Social</label>
            <select name="id_obra_social">
                <option value="1" <?= $p['id_obra_social']==1?'selected':'' ?>>Particular</option>
                <option value="2" <?= $p['id_obra_social']==2?'selected':'' ?>>OSDE</option>
                <option value="3" <?= $p['id_obra_social']==3?'selected':'' ?>>PAMI</option>
            </select>
            <button type="submit" class="btn">Guardar Cambios</button>
            <a href="lista_pacientes.php" style="display:block; text-align:center; margin-top:15px; color:#666; text-decoration:none; font-size:0.8rem;">Volver</a>
        </form>
    </div>
</body>
</html>
