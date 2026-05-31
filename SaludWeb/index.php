<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$status = [
    'db' => false,
    'message' => 'Sin verificar.'
];

try {
    require_once __DIR__ . '/db.php';
    $status['db'] = true;
    $status['message'] = 'Conexión OK.';
    $status['pacientes'] = (int)$pdo->query('SELECT COUNT(*) FROM pacientes')->fetchColumn();
    $status['medicos'] = (int)$pdo->query('SELECT COUNT(*) FROM medicos')->fetchColumn();
    $status['obras'] = (int)$pdo->query('SELECT COUNT(*) FROM obras_sociales')->fetchColumn();
} catch (Exception $e) {
    $status['message'] = $e->getMessage();
}
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SaludWEB - Inicio</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 32px; background: #f4f6fb; color: #1f2937; }
        .card { background: white; border-radius: 18px; padding: 28px; max-width: 900px; margin: 0 auto; box-shadow: 0 18px 40px rgba(15,23,42,0.08); }
        h1 { margin: 0 0 16px; font-size: 2rem; }
        .status { display: grid; gap: 14px; margin-bottom: 26px; }
        .badge { display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 999px; font-weight: 700; }
        .success { background: #d1fae5; color: #065f46; }
        .error { background: #fee2e2; color: #991b1b; }
        .actions { display: grid; gap: 12px; }
        .actions a { display: inline-block; padding: 12px 18px; background: #2563eb; color: white; text-decoration: none; border-radius: 12px; }
        .actions a.secondary { background: #475569; }
        .notice { background: #eef2ff; padding: 14px 18px; border-radius: 12px; border-left: 4px solid #2563eb; }
        .meta { margin-top: 20px; display: grid; grid-template-columns: repeat(auto-fit,minmax(140px,1fr)); gap: 14px; }
        .meta-item { background: #f8fafc; padding: 16px; border-radius: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Bienvenido a SaludWEB</h1>
        <div class="status">
            <div class="badge <?php echo $status['db'] ? 'success' : 'error'; ?>">
                <?php echo $status['db'] ? 'Base de datos conectada' : 'Error de conexión'; ?>
            </div>
            <div class="notice">
                <strong>Detalle:</strong> <?php echo htmlspecialchars($status['message']); ?>
            </div>
        </div>

        <?php if ($status['db']): ?>
            <div class="actions">
                <a href="/prog3-clase2/lista">Ir al Dashboard</a>
                <a href="/prog3-clase2/registro" class="secondary">Registrar Paciente</a>
                <a href="papelera.php" class="secondary">Ver Papelera</a>
                <a href="escritorio.php" class="secondary">Ir al Escritorio</a>
            </div>
            <div class="meta">
                <div class="meta-item"><strong>Pacientes</strong><p><?php echo $status['pacientes']; ?></p></div>
                <div class="meta-item"><strong>Médicos</strong><p><?php echo $status['medicos']; ?></p></div>
                <div class="meta-item"><strong>Obras sociales</strong><p><?php echo $status['obras']; ?></p></div>
            </div>
        <?php else: ?>
            <p>Abre el archivo <code>SaludWeb/db.php</code> y revisa los parámetros de conexión o los permisos de MySQL.</p>
        <?php endif; ?>
    </div>
</body>
</html>
