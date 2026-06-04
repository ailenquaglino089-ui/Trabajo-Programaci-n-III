<?php
/**
 * Módulo de Papelera: Recupera pacientes que han sido marcados como inactivos (borrado lógico).
 */
require_once __DIR__ . '/db.php';

$sql = "SELECT * FROM pacientes WHERE activo = 0 ORDER BY nombre ASC";
$stmt = $pdo->query($sql);
$eliminados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelera - SaludWEB</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; }
        .box { background: white; padding: 25px; border-radius: 18px; max-width: 800px; margin: auto; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; color: #475569; border-bottom: 2px solid #e2e8f0; padding: 12px; }
        td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
        .btn-volver { text-decoration: none; color: #007bff; font-weight: bold; display: inline-block; margin-bottom: 12px; }
        @media (max-width: 700px) {
            body { padding: 14px; }
            .box { padding: 18px; }
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { position: absolute; top: -9999px; left: -9999px; }
            tr { margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; }
            td { border: none; padding: 12px 10px; }
            td::before { content: attr(data-label); display: block; font-weight: 700; margin-bottom: 6px; }
        }
    </style>
</head>
<body>
    <div class="box">
        <a href="lista_pacientes.php" class="btn-volver">← Volver al Dashboard</a>
        <h2>🗑️ Pacientes en Papelera</h2>
        <table>
            <thead>
                <tr><th>PACIENTE</th><th>ACCIONES</th></tr>
            </thead>
            <tbody>
                <?php foreach ($eliminados as $e): ?>
                <tr>
                    <td data-label="Paciente"><strong><?php echo htmlspecialchars($e['nombre']); ?></strong></td>
                    <td data-label="Acciones">
                        <a href="restaurar_paciente.php?id=<?php echo $e['id']; ?>" title="Restaurar" style="text-decoration:none; font-size:1.2rem;">🔄</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
