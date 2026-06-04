<?php
require_once __DIR__ . '/db.php';
$logs = $pdo->query("SELECT * FROM auditoria ORDER BY fecha DESC LIMIT 50")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría</title>
    <style>
        body { font-family: sans-serif; padding: 30px; background: #f4f7f6; }
        .page { max-width: 1100px; margin: auto; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 14px; overflow: hidden; }
        th, td { padding: 15px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #343a40; color: white; }
        a { display: inline-block; margin-bottom: 18px; color: #007bff; text-decoration: none; font-weight: bold; }
        @media (max-width: 840px) {
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { position: absolute; top: -9999px; left: -9999px; }
            tr { margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; }
            td { border: none; padding: 12px 10px; }
            td::before { content: attr(data-label); display: block; font-weight: 700; margin-bottom: 8px; }
        }
    </style>
</head>
<body>
    <div class="page">
    <h1>Historial de Auditoría</h1>
    <a href="lista_pacientes.php">← Volver al Dashboard</a><br><br>
    <table>
        <thead><tr><th>Fecha</th><th>Acción</th></tr></thead>
        <tbody>
            <?php foreach($logs as $l): ?>
            <tr>
                <td data-label="Fecha"><?php echo $l['fecha']; ?></td>
                <td data-label="Acción">
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
    </div>
</body>
</html>
