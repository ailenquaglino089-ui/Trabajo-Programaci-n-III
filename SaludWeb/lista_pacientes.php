<?php
// SaludWEB/lista_pacientes.php

// 1. Iniciamos sesión al principio de todo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $pdo; 
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos (aseguramos la ruta correcta)
require_once __DIR__ . '/db.php';

try {
    // Lógica para la lupa de búsqueda
    $busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
    
    $sql = "SELECT p.*, o.nombre_obra, t.nivel_gravedad 
            FROM pacientes p 
            LEFT JOIN obras_sociales o ON p.id_obra_social = o.id 
            LEFT JOIN (
                SELECT id_paciente, nivel_gravedad 
                FROM triages 
                WHERE id IN (SELECT MAX(id) FROM triages GROUP BY id_paciente)
            ) t ON p.id = t.id_paciente
            WHERE p.activo = 1 AND (p.nombre LIKE ? OR p.dni LIKE ?)
            ORDER BY t.nivel_gravedad DESC, p.nombre ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$busqueda%", "%$busqueda%"]);
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error en la conexión: " . $e->getMessage());
}

// Cálculos para las tarjetas de estadísticas
$total = count($pacientes);
$criticos = 0; $suma_dolor = 0; $con_triage = 0;
$c_part = 0; $c_osde = 0; $c_pami = 0;

foreach($pacientes as $pac) {
    $g = $pac['nivel_gravedad'];
    if($g !== null) {
        if($g >= 8) $criticos++;
        $suma_dolor += $g;
        $con_triage++;
    }
    // Lógica de obras sociales (Ajustar IDs según tu tabla obras_sociales)
    if($pac['id_obra_social'] == 1) $c_part++;
    elseif($pac['id_obra_social'] == 2) $c_osde++;
    elseif($pac['id_obra_social'] == 3) $c_pami++;
}
$promedio_dolor = ($con_triage > 0) ? round($suma_dolor / $con_triage, 1) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SaludWEB Pro - Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 1250px; margin: auto; }
        /* Estilo para las alertas de sesión */
        .alert-session { background: #d4edda; color: #155724; padding: 15px; border-radius: 12px; border-left: 6px solid #28a745; margin-bottom: 25px; font-weight: bold; }
        .alert-danger { background: #f8d7da; color: #721c24; border-left-color: #dc3545; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; color: white; display: inline-block; }
        .btn-nuevo { background: #007bff; }
        .btn-papelera { background: #6c757d; margin-right: 10px; }
        .search-bar { background: white; padding: 15px; border-radius: 10px; margin-bottom: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .search-bar input { border: none; outline: none; width: 90%; font-size: 1rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; }
        .stat-card i { font-size: 2rem; display: block; margin-bottom: 5px; }
        .stat-card h3 { font-size: 0.9rem; color: #666; margin: 10px 0; }
        .stat-card p { font-size: 1.8rem; font-weight: bold; margin: 0; }
        .main-layout { display: grid; grid-template-columns: 2.5fr 1fr; gap: 25px; }
        .white-box { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: #999; font-size: 0.8rem; padding: 10px; border-bottom: 2px solid #f0f0f0; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #f8f8f8; }
        .badge { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.8rem; }
        .bg-urgente { background: #dc3545; } .bg-riesgo { background: #ffc107; color: #333; } 
        .bg-estable { background: #28a745; } .bg-pendiente { background: #6c757d; }
        .txt-obra { font-size: 0.8rem; color: #555; background: #eef2f7; padding: 4px 8px; border-radius: 6px; font-weight: 600; }
        .acciones a { text-decoration: none; margin-right: 8px; font-size: 1.1rem; }
    </style>
</head>
<body>

<div class="container">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert-session <?php echo ($_SESSION['tipo_mensaje'] ?? '') == 'danger' ? 'alert-danger' : ''; ?>">
            <?php echo ($_SESSION['tipo_mensaje'] ?? '') == 'danger' ? '⚠️' : '✅'; ?> 
            <?php echo $_SESSION['mensaje']; ?>
        </div>
        <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
    <?php endif; ?>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert-session">✅ <?php echo htmlspecialchars($_GET['mensaje']); ?></div>
    <?php endif; ?>

    <div class="header">
        <h1>🏥 SaludWEB <span style="color:#007bff;">Pro</span></h1>
        <div>
            <a href="escritorio" class="btn btn-nuevo" style="background:#6f42c1; margin-right:10px;">🧩 Escritorio</a>
            <a href="api-docs" class="btn btn-nuevo" style="background:#17a2b8; margin-right:10px;">📄 API Docs</a>
            <a href="chat" class="btn btn-nuevo" style="background:#28a745; margin-right:10px;">💬 Asistente AI</a>
            <a href="emitir_prescripcion" class="btn btn-nuevo" style="background:#ff6b35; margin-right:10px;">💊 MRx Digital - Emitir Receta</a>
            <a href="lista_prescripciones" class="btn btn-nuevo" style="background:#ff6b35; margin-right:10px;">📋 Ver Recetas</a>
            <a href="papelera" class="btn btn-papelera">🗑️ Ver Papelera</a>
            <a href="registro" class="btn btn-nuevo">+ Registrar Nuevo Paciente</a>
        </div>
    </div>

    <form class="search-bar" method="GET">
        🔍 <input type="text" name="buscar" placeholder="Buscar por nombre o DNI..." value="<?php echo htmlspecialchars($busqueda); ?>">
    </form>

    <div class="stats-grid">
        <div class="stat-card"><i>👥</i><h3>Pacientes Totales</h3><p><?php echo $total; ?></p></div>
        <div class="stat-card"><i>🚨</i><h3>Casos Críticos</h3><p style="color:#dc3545;"><?php echo $criticos; ?></p></div>
        <div class="stat-card"><i>🌡️</i><h3>Dolor Promedio</h3><p style="color:#007bff;"><?php echo $promedio_dolor; ?></p></div>
        <div class="stat-card"><i>🛡️</i><h3>Sistema</h3><p style="color:#28a745;">OK</p></div>
    </div>

    <div class="main-layout">
        <div class="white-box">
            <h2>Lista de Atención</h2>
            <table>
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Paciente</th>
                        <th>DNI</th>
                        <th>Obra Social</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $p): 
                        $g = $p['nivel_gravedad'];
                        $clase = ($g === null) ? "bg-pendiente" : (($g >= 8) ? "bg-urgente" : (($g >= 5) ? "bg-riesgo" : "bg-estable"));
                    ?>
                    <tr>
                        <td><div class="badge <?php echo $clase; ?>"><?php 
                            if ($g === null) {
                                echo '⏳';
                            } elseif ($g >= 8) {
                                echo '🚨';
                            } elseif ($g >= 5) {
                                echo '⚠️';
                            } else {
                                echo '✅';
                            }
                        ?></div></td>
                        <td><strong><?php echo htmlspecialchars($p['nombre']); ?></strong></td>
                        <td><?php echo htmlspecialchars($p['dni']); ?></td>
                        <td><span class="txt-obra"><?php echo htmlspecialchars($p['nombre_obra'] ?? 'Particular'); ?></span></td>
                        <td class="acciones">
                            <a href="ver_triage?id=<?php echo $p['id']; ?>" title="Ver">👁️</a>
                            <a href="triage?id=<?php echo $p['id']; ?>" title="Triage">📋</a>
                            <a href="editar?id=<?php echo $p['id']; ?>" title="Editar">✏️</a>
                            <a href="eliminar?id=<?php echo $p['id']; ?>" onclick="return confirm('¿Enviar a la papelera?')" title="Eliminar">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="white-box">
            <h3 style="text-align:center; margin-bottom:20px;">Obras Sociales</h3>
            <canvas id="graficoObras"></canvas>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('graficoObras').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Particular', 'OSDE', 'PAMI'],
        datasets: [{
            data: [<?php echo "$c_part, $c_osde, $c_pami"; ?>],
            backgroundColor: ['#ffcd56', '#36a2eb', '#ff6384'],
            borderWidth: 0
        }]
    },
    options: { 
        cutout: '70%', 
        plugins: { legend: { position: 'bottom' } } 
    }
});
</script>
</body>
</html>
