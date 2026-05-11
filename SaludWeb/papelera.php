<?php
/**
 * Módulo de Papelera: Recupera pacientes que han sido marcados como inactivos (borrado lógico).
 */
require_once __DIR__ . '/db.php';;

$sql = "SELECT * FROM pacientes WHERE activo = 0 ORDER BY nombre ASC";
$stmt = $pdo->query($sql);
$eliminados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Papelera - SaludWEB</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 40px; }
        .box { background: white; padding: 25px; border-radius: 15px; max-width: 800px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; color: #999; border-bottom: 2px solid #eee; padding: 10px; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .btn-volver { text-decoration: none; color: #007bff; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <a href="lista" class="btn-volver">← Volver al Dashboard</a>
        <h2>🗑️ Pacientes en Papelera</h2>
        <table>
            <thead>
                <tr><th>PACIENTE</th><th>ACCIONES</th></tr>
            </thead>
            <tbody>
                <?php foreach ($eliminados as $e): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($e['nombre']); ?></strong></td>
                    <td>
                        <a href="restaurar?id=<?php echo $e['id']; ?>" title="Restaurar" style="text-decoration:none; font-size:1.2rem;">🔄</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
