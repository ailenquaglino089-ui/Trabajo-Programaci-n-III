<?php
require_once __DIR__ . '/db.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: lista_pacientes.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $obra = $_POST['id_obra_social'];

    $sql = "UPDATE pacientes SET nombre = ?, dni = ?, id_obra_social = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $dni, $obra, $id]);

    // Redirigir con mensaje para que aparezca la alerta
    header("Location: lista_pacientes.php?mensaje=Paciente actualizado con éxito");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Paciente</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 350px; }
        h2 { color: #007bff; text-align: center; margin-bottom: 20px; }
        input, select { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; }
        .btn { background: #007bff; color: white; border: none; width: 100%; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; }
        .cancel { display: block; text-align: center; margin-top: 15px; color: #888; text-decoration: none; }
    </style>
</head>
<body>

<div class="card">
    <h2>✏️ Editar Datos</h2>
    <form method="POST">
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($p['nombre']); ?>" placeholder="Nombre completo" required>
        <input type="text" name="dni" value="<?php echo htmlspecialchars($p['dni']); ?>" placeholder="DNI" required>
        <select name="id_obra_social">
            <option value="1" <?php if($p['id_obra_social']==1) echo 'selected'; ?>>Particular</option>
            <option value="2" <?php if($p['id_obra_social']==2) echo 'selected'; ?>>OSDE</option>
            <option value="3" <?php if($p['id_obra_social']==3) echo 'selected'; ?>>PAMI</option>
        </select>
        <button type="submit" class="btn">Guardar Cambios</button>
        <a href="lista_pacientes.php" class="cancel">Volver sin cambios</a>
    </form>
</div>

</body>
</html>
