<?php
require_once __DIR__ . '/db.php';
$logs = $pdo->query("SELECT * FROM auditoria ORDER BY fecha DESC LIMIT 50")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Auditoría</title>
    <style>
        body { font-family: sans-serif; padding: 30px; background: #f4f7f6; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; }
        th, td { padding: 15px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #343a40; color: white; }
    </style>
</head>
<body>
    <h1>Historial de Auditoría</h1>
    <a href="lista_pacientes.php">← Volver al Dashboard</a><br><br>
    <table>
        <thead><tr><th>Fecha</th><th>Acción</th></tr></thead>
        <tbody>
            <?php foreach($logs as $l): ?>
            <tr>
                <td><?php echo $l['fecha']; ?></td>
                <td>
                    <?php echo $l['accion']; ?>
                    <?php if (strpos($l['accion'], 'papelera') !== false): 
                        $id = filter_var($l['accion'], FILTER_SANITIZE_NUMBER_INT); ?>
                        <a href="restaurar_paciente.php?id=<?php echo $id; ?>" style="color: green; font-weight: bold; margin-left: 20px; text-decoration: none;">[♻️ Restaurar]</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
