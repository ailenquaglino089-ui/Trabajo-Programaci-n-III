<?php
// SaludWEB/lista_pacientes.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $pdo; 
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/db.php';

try {
    $busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
    
    // Consulta compleja: Une pacientes con sus obras sociales y obtiene solo el ÚLTIMO triage realizado
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

// Procesamiento de indicadores para el Dashboard (Business Intelligence básico)
$total = count($pacientes);
$criticos = 0; $suma_dolor = 0; $con_triage = 0; $pendientes = 0;
$obras_stats = [];

foreach($pacientes as $pac) {
    $g = $pac['nivel_gravedad'];
    if($g !== null) {
        if($g >= 8) $criticos++;
        $suma_dolor += $g;
        $con_triage++;
    } else {
        $pendientes++;
    }
    $obra_nombre = $pac['nombre_obra'] ?? 'Particular';
    $obras_stats[$obra_nombre] = ($obras_stats[$obra_nombre] ?? 0) + 1;
}
$promedio_dolor = ($con_triage > 0) ? round($suma_dolor / $con_triage, 1) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SaludWEB Pro - Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #ff9f1c;
            --bg: #f8f9fc;
            --white: #ffffff;
            --text: #2b2d42;
            --gray: #8d99ae;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 20px; }
        .container { max-width: 1400px; margin: auto; }
        
        .alert-session { background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 12px; border-left: 5px solid #10b981; margin-bottom: 1.5rem; font-weight: 500; }
        .alert-danger { background: #fee2e2; color: #991b1b; border-left-color: #ef4444; }
        
        .header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; }
        .header h1 { margin: 0; font-size: 1.8rem; font-weight: 700; }
        
        .action-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px; margin-bottom: 2rem; }
        .action-card { background: var(--white); padding: 20px; border-radius: 16px; text-decoration: none; color: inherit; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #edf2f7; transition: all 0.2s; display: flex; flex-direction: column; align-items: center; text-align: center; }
        .action-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-color: var(--primary); }
        .action-card i { font-size: 1.6rem; margin-bottom: 8px; }
        .action-card span { font-weight: 600; font-size: 0.9rem; }

        .nav-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { text-decoration: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; color: white; display: inline-flex; align-items: center; transition: all 0.2s; border: none; cursor: pointer; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-primary { background: var(--primary); }
        .btn-secondary { background: var(--gray); }
        .btn-success { background: #10b981; }
        .btn-outline { background: transparent; color: var(--primary); border: 1px solid var(--primary); }
        
        .search-bar { background: white; padding: 12px 20px; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); display: flex; align-items: center; }
        .search-bar input { border: none; outline: none; width: 100%; font-size: 1rem; margin-left: 10px; color: var(--text); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 15px; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stat-info h3 { font-size: 0.8rem; color: var(--gray); margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-info p { font-size: 1.5rem; font-weight: 700; margin: 0; }
        
        .main-layout { display: grid; grid-template-columns: 2.5fr 1fr; gap: 25px; }
        .white-box { background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        .white-box h2 { font-size: 1.2rem; margin-top: 0; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: var(--gray); font-size: 0.75rem; padding: 12px; border-bottom: 2px solid #f1f5f9; text-transform: uppercase; }
        td { padding: 14px 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        tr:hover { background: #f8fafc; }
        
        .badge-status { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; color: white; text-transform: uppercase; }
        .bg-urgente { background: var(--danger); } 
        .bg-riesgo { background: var(--warning); color: #000; } 
        .bg-estable { background: #10b981; } 
        .bg-pendiente { background: var(--gray); }
        
        .txt-obra { font-size: 0.75rem; color: var(--primary); background: #e0e7ff; padding: 4px 10px; border-radius: 6px; font-weight: 600; }
        
        .acciones { display: flex; gap: 8px; }
        .acciones a { text-decoration: none; padding: 6px; border-radius: 6px; background: #f1f5f9; transition: background 0.2s; }
        .acciones a:hover { background: #e2e8f0; }
        
        .empty-state { text-align: center; padding: 40px; color: var(--gray); }
        
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .main-layout { grid-template-columns: 1fr; }
        }
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
        <div class="nav-actions">
             <a href="escritorio" class="btn btn-outline">⚙️ Configuración</a>
             <span style="color:var(--gray); font-size:0.8rem;">v2.5 Stable</span>
        </div>
    </div>

    <!-- Panel de Acciones Rápidas Organizado -->
    <div class="action-grid">
        <!-- Gestión de Pacientes -->
        <a href="registro" class="action-card" style="border-top: 3px solid var(--primary);">
            <i>➕</i>
            <span>Registrar Nuevo Paciente</span>
        </a>

        <!-- Módulo MRx Digital -->
        <a href="emitir_prescripcion" class="action-card" style="border-top: 3px solid #ff6b35;">
            <i>💊</i>
            <span>MRx Digital-Emitir Recetas</span>
        </a>
        <a href="lista_prescripciones" class="action-card" style="border-top: 3px solid #ff6b35;">
            <i>📂</i>
            <span>Ver Recetas</span>
        </a>

        <!-- Herramientas Inteligentes -->
        <a href="chat" class="action-card" style="border-top: 3px solid #10b981;">
            <i>💬</i>
            <span>Asistente AI</span>
        </a>

        <!-- Sistema y Utilidades -->
        <a href="escritorio" class="action-card" style="border-top: 3px solid #6366f1;">
            <i>🧩</i>
            <span>Escritorio</span>
        </a>
        <a href="api-docs" class="action-card" style="border-top: 3px solid var(--gray);">
            <i>📄</i>
            <span>API Docs</span>
        </a>
        <a href="papelera" class="action-card" style="border-top: 3px solid var(--danger);">
            <i>🗑️</i>
            <span>Ver Papelera</span>
        </a>
    </div>

    <form class="search-bar" method="GET">
        🔍 <input type="text" name="buscar" placeholder="Buscar por nombre o DNI..." value="<?php echo htmlspecialchars($busqueda); ?>">
    </form>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e0e7ff; color:var(--primary);">👥</div>
            <div class="stat-info"><h3>Total Pacientes</h3><p><?php echo $total; ?></p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7; color:var(--warning);">⏳</div>
            <div class="stat-info"><h3>Pendientes Triage</h3><p><?php echo $pendientes; ?></p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2; color:var(--danger);">🚨</div>
            <div class="stat-info"><h3>Casos Críticos</h3><p style="color:var(--danger);"><?php echo $criticos; ?></p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e0f2fe; color:var(--primary);">🌡️</div>
            <div class="stat-info"><h3>Dolor Promedio</h3><p><?php echo $promedio_dolor; ?></p></div>
        </div>
    </div>

    <div class="main-layout">
        <div class="white-box">
            <h2>
                <span>📋 Lista de Atención</span>
                <?php if($busqueda): ?><small style="font-weight:400; color:var(--gray); font-size:0.9rem;">(Filtrado por: <?php echo htmlspecialchars($busqueda); ?>)</small><?php endif; ?>
            </h2>
            <?php if ($total > 0): ?>
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
                        $texto = ($g === null) ? "Pendiente" : (($g >= 8) ? "Emergencia" : (($g >= 5) ? "Urgente" : "Estable"));
                    ?>
                    <tr>
                        <td><span class="badge-status <?php echo $clase; ?>"><?php echo $texto; ?></span></td>
                        <td><strong><?php echo htmlspecialchars($p['nombre']); ?></strong></td>
                        <td style="color:var(--gray);"><?php echo htmlspecialchars($p['dni']); ?></td>
                        <td><span class="txt-obra"><?php echo htmlspecialchars($p['nombre_obra'] ?? 'Particular'); ?></span></td>
                        <td class="acciones">
                            <a href="ver_triage?id=<?php echo $p['id']; ?>" title="Historial">👁️</a>
                            <a href="triage?id=<?php echo $p['id']; ?>" title="Nuevo Triage" style="background:#d1fae5;">📋</a>
                            <a href="editar?id=<?php echo $p['id']; ?>" title="Editar">✏️</a>
                            <a href="eliminar?id=<?php echo $p['id']; ?>" onclick="return confirm('¿Enviar a la papelera?')" title="Eliminar" style="background:#fee2e2;">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="empty-state">
                    <p style="font-size:3rem;">🔎</p>
                    <p>No se encontraron pacientes activos.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="white-box">
            <h3 style="margin-top:0;">📊 Coberturas</h3>
            <div style="position:relative; height:250px;">
                <canvas id="graficoObras"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('graficoObras').getContext('2d');
const obrasData = <?php echo json_encode($obras_stats); ?>;

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(obrasData),
        datasets: [{
            data: Object.values(obrasData),
            backgroundColor: ['#4361ee', '#4cc9f0', '#f72585', '#ff9f1c', '#10b981'],
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
