<?php
require_once __DIR__ . '/db.php';;

// Verificamos que venga el ID por la URL
if (!isset($_GET['id'])) {
    die("ID de paciente no proporcionado.");
}

$id_paciente = $_GET['id'];

try {
    // 1. Buscamos los datos del paciente
    $stmtP = $pdo->prepare("SELECT * FROM pacientes WHERE id = ?");
    $stmtP->execute([$id_paciente]);
    $paciente = $stmtP->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        die("Paciente no encontrado.");
    }

    // 2. Buscamos el historial de triages (Ordenados por ID, el más nuevo arriba)
    // Quitamos 'fecha_triage' para evitar el error fatal
    $stmtT = $pdo->prepare("SELECT * FROM triages WHERE id_paciente = ? ORDER BY id DESC");
    $stmtT->execute([$id_paciente]);
    $triages = $stmtT->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Triage - <?php echo htmlspecialchars($paciente['nombre']); ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .info-paciente { background: #eef2f7; padding: 15px; border-radius: 10px; margin-bottom: 25px; }
        .triage-card { border-left: 5px solid #007bff; background: #fff; padding: 15px; margin-bottom: 15px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; color: white; font-weight: bold; font-size: 0.9rem; }
        .btn-volver { display: inline-block; margin-top: 20px; text-decoration: none; color: #007bff; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>Detalles del Paciente</h1>
    
    <div class="info-paciente">
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($paciente['nombre']); ?></p>
        <p><strong>DNI:</strong> <?php echo htmlspecialchars($paciente['dni']); ?></p>
    </div>

    <h2>Historial de Triages</h2>

    <?php if (count($triages) > 0): ?>
        <?php foreach ($triages as $t): 
            $dolor = $t['nivel_gravedad'];
            $color = ($dolor >= 8) ? '#dc3545' : (($dolor >= 5) ? '#ffc107' : '#28a745');
        ?>
            <div class="triage-card">
                <p><strong>Nivel de Dolor:</strong> 
                    <span class="badge" style="background: <?php echo $color; ?>;">
                        <?php echo $dolor; ?> / 10
                    </span>
                </p>
                <p><strong>Síntomas:</strong> <?php echo htmlspecialchars($t['sintomas'] ?? 'No especificado'); ?></p>
                <p><strong>Observaciones:</strong> <?php echo htmlspecialchars($t['observaciones'] ?? 'Sin observaciones'); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Este paciente aún no tiene triages registrados.</p>
    <?php endif; ?>

    <a href="lista" class="btn-volver">← Volver a la lista</a>
</div>

</body>
</html>
