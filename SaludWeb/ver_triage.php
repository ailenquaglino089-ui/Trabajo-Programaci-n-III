<?php
require_once __DIR__ . '/db.php';

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

    // 3. Buscamos el historial de recetas emitidas
    $stmtR = $pdo->prepare("SELECT pr.*, m.nombre AS medico_nombre 
                            FROM prescripciones pr 
                            JOIN medicos m ON pr.id_medico = m.id 
                            WHERE pr.id_paciente = ? 
                            ORDER BY pr.fecha_emision DESC");
    $stmtR->execute([$id_paciente]);
    $recetas = $stmtR->fetchAll(PDO::FETCH_ASSOC);

    // Mapa de medicamentos para mostrar nombres legibles
    $mapaMedicamentos = $pdo->query("SELECT id, nombre FROM medicamentos")->fetchAll(PDO::FETCH_KEY_PAIR);

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente - <?php echo htmlspecialchars($paciente['nombre']); ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 30px; border-radius: 18px; box-shadow: 0 16px 30px rgba(0,0,0,0.08); }
        h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .info-paciente { background: #eef2f7; padding: 18px; border-radius: 16px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; gap: 16px; }
        .section-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .card { background: #fff; border: 1px solid #eee; padding: 18px; margin-bottom: 15px; border-radius: 16px; }
        .triage-card { border-left: 5px solid #007bff; }
        .receta-card { border-left: 5px solid #10b981; }
        .badge { display: inline-block; padding: 6px 12px; border-radius: 999px; color: white; font-weight: 700; font-size: 0.9rem; }
        .btn-volver { display: inline-block; margin-top: 20px; text-decoration: none; color: #007bff; font-weight: bold; }
        .med-item { font-size: 0.9rem; color: #475569; margin-bottom: 6px; }
        @media (max-width: 900px) {
            .section-grid { grid-template-columns: 1fr; }
            .info-paciente { flex-direction: column; align-items: flex-start; }
        }
        @media (max-width: 680px) {
            body { padding: 14px; }
            .container { padding: 20px; }
            .info-paciente { padding: 16px; }
            .card { padding: 14px; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📂 Expediente Clínico Digital</h1>
    
    <div class="info-paciente">
        <div>
            <p style="margin:0; font-size: 1.2rem;"><strong><?php echo htmlspecialchars($paciente['nombre']); ?></strong></p>
            <p style="margin:5px 0 0 0; color: #666;">DNI: <?php echo htmlspecialchars($paciente['dni']); ?></p>
        </div>
        <a href="lista_pacientes.php" class="btn-volver">← Volver al Dashboard</a>
    </div>

    <div class="section-grid">
        <div>
            <h3>📋 Evolución de Triage</h3>
            <?php if (count($triages) > 0): ?>
                <?php foreach ($triages as $t): 
                    $dolor = (int)$t['nivel_gravedad'];
                    $color = ($dolor >= 8) ? '#ef4444' : (($dolor >= 5) ? '#f59e0b' : '#10b981');
                ?>
                    <div class="card triage-card">
                        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                            <span class="badge" style="background:<?php echo $color; ?>;">Dolor: <?php echo $dolor; ?>/10</span>
                            <small><?php echo date('d/m/Y H:i', strtotime($t['fecha'])); ?></small>
                        </div>
                        <p class="med-item"><strong>Observaciones:</strong> <?php echo htmlspecialchars($t['observaciones'] ?: 'Sin notas'); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#888;">No hay registros de triage.</p>
            <?php endif; ?>
        </div>

        <div>
            <h3>💊 Historial de Prescripciones</h3>
            <?php if (count($recetas) > 0): ?>
                <?php foreach ($recetas as $r): ?>
                    <div class="card receta-card">
                        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                            <span class="badge" style="background:<?php echo ($r['estado'] == 'activa') ? '#10b981' : '#64748b'; ?>;"><?php echo strtoupper($r['estado']); ?></span>
                            <small><?php echo date('d/m/Y', strtotime($r['fecha_emision'])); ?></small>
                        </div>
                        <p class="med-item"><strong>Médico:</strong> <?php echo htmlspecialchars($r['medico_nombre']); ?></p>
                        <div style="margin-top:8px; border-top: 1px solid #f0f0f0; padding-top:8px;">
                            <?php 
                            foreach (json_decode($r['medicamentos'], true) as $med) {
                                $nombre = $mapaMedicamentos[$med['id']] ?? 'Desconocido';
                                echo "<div class='med-item'>• " . htmlspecialchars($nombre) . " (" . htmlspecialchars($med['dosis']) . ")</div>";
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#888;">Sin recetas emitidas.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
