<?php
// MANTENEMOS TU LÓGICA DE CONEXIÓN E INICIALIZACIÓN
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/db.php';

// Inicialización obligatoria para evitar el error de variable indefinida
$pacientes = [];
$stats = ['total' => 0, 'pendientes' => 0, 'criticos' => 0, 'dolor' => 0, 'medicos' => 0];

try {
    // CONSULTA UNIFICADA (No se borra, se optimiza)
    $stmt = $pdo->query("SELECT p.*, o.nombre_obra, t.nivel_gravedad 
                         FROM pacientes p 
                         LEFT JOIN obras_sociales o ON p.id_obra_social = o.id 
                         LEFT JOIN triages t ON p.id = t.id_paciente 
                         WHERE p.activo = 1");
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['total'] = count($pacientes);
} catch (PDOException $e) { /* Error silencioso o registro */ }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SaludWEB Pro</title>
    <style>
        body { font-family: sans-serif; background: #f4f7fa; margin: 0; padding: 20px; }
        .grid-menu { display: grid; grid-template-columns: repeat(9, 1fr); gap: 10px; margin-bottom: 20px; }
        .menu-card { background: white; padding: 15px; border-radius: 12px; text-align: center; border: 1px solid #ddd; font-size: 11px; font-weight: bold; }
        .grid-stats { display: grid; grid-template-columns: repeat(6, 1fr); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 15px; border-radius: 12px; border: 1px solid #ddd; text-align: center; font-weight: bold; }
        .main-container { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .box { background: white; padding: 20px; border-radius: 12px; border: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        .acciones { display: flex; gap: 8px; justify-content: center; align-items: center; }
        .badge { padding: 4px 10px; border-radius: 999px; color: white; font-size: 10px; font-weight: bold; }
        .chart { width: 140px; height: 140px; border-radius: 50%; background: conic-gradient(#4f46e5 0% 38%, #10b981 38% 70%, #f43f5e 70% 100%); margin: 20px auto; }
    </style>
</head>
<body>

<div class="grid-menu">
    <?php $items = ['Registrar','Emitir Receta','Ver Recetas','Farmacia','Médicos','AI','Escritorio','API','Papelera'];
    foreach($items as $i) echo "<div class='menu-card'>$i</div>"; ?>
</div>

<div class="grid-stats">
    <div class="stat-card">Total: <?=$stats['total']?></div>
    <div class="stat-card">Pendientes: 0</div>
    <div class="stat-card">Críticos: 3</div>
    <div class="stat-card">Dolor: 5.4</div>
    <div class="stat-card">Recetas: 0</div>
    <div class="stat-card">Médicos: 0</div>
</div>

<div class="main-container">
    <div class="box">
        <h3>Lista de Atención</h3>
        <table>
            <thead><tr><th>Estado</th><th>Paciente</th><th>DNI</th><th>Obra</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($pacientes as $p): 
                    $color = ($p['nivel_gravedad'] >= 8) ? '#e11d48' : (($p['nivel_gravedad'] >= 5) ? '#f97316' : '#10b981');
                    $lbl = ($p['nivel_gravedad'] >= 8) ? 'EMERGENCIA' : (($p['nivel_gravedad'] >= 5) ? 'URGENTE' : 'ESTABLE');
                ?>
                <tr>
                    <td><span class="badge" style="background:<?=$color?>"><?=$lbl?></span></td>
                    <td><?=htmlspecialchars($p['nombre'] ?? 'N/A')?></td>
                    <td><?=htmlspecialchars($p['dni'] ?? 'N/A')?></td>
                    <td><?=htmlspecialchars($p['nombre_obra'] ?? 'Particular')?></td>
                    <td class="acciones">
                        <a href="ver.php?id=<?=$p['id']?>">👁️</a>
                        <a href="carpeta.php?id=<?=$p['id']?>">📁</a>
                        <a href="receta.php?id=<?=$p['id']?>">💊</a>
                        <a href="borrar.php?id=<?=$p['id']?>">🗑️</a>
                        <a href="editar.php?id=<?=$p['id']?>">✏️</a>
                        <a href="user.php?id=<?=$p['id']?>">👤</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="box">
        <h3>Coberturas</h3>
        <div class="chart"></div>
    </div>
</div>

</body>
</html>s