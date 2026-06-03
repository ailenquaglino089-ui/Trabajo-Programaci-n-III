<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/db.php';
$pacientes = [];
try {
    $search = $_GET['q'] ?? '';

    // Consulta mejorada para traer el ÚLTIMO triage de cada paciente
    $sql = "SELECT p.*, o.nombre_obra, 
            (SELECT nivel_gravedad FROM triages WHERE id_paciente = p.id ORDER BY fecha DESC LIMIT 1) as nivel_gravedad 
            FROM pacientes p 
            LEFT JOIN obras_sociales o ON p.id_obra_social = o.id 
            WHERE p.activo = 1";
    
    if ($search !== '') {
        $sql .= " AND (p.nombre LIKE :q OR p.dni LIKE :q)";
    }
    
    $sql .= " ORDER BY nivel_gravedad DESC, p.nombre ASC";

    $stmt = $pdo->prepare($sql);
    if ($search !== '') { $stmt->bindValue(':q', "%$search%"); }
    $stmt->execute();
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cálculos para las métricas (Globales)
    $totalPacientes = $pdo->query("SELECT COUNT(*) FROM pacientes WHERE activo = 1")->fetchColumn();
    $pendientesTriage = $pdo->query("SELECT COUNT(*) FROM pacientes p LEFT JOIN triages t ON p.id = t.id_paciente WHERE t.id IS NULL AND p.activo = 1")->fetchColumn();
    $criticos = count(array_filter($pacientes, fn($p) => ($p['nivel_gravedad'] ?? 0) >= 8));
    $niveles = array_filter(array_column($pacientes, 'nivel_gravedad'), fn($v) => !is_null($v));
    $promedioDolor = count($niveles) > 0 ? round(array_sum($niveles) / count($niveles), 1) : 0;

    $medicosStat = $pdo->query("SELECT COUNT(CASE WHEN activo = 1 THEN 1 END) as activos, COUNT(CASE WHEN activo = 0 THEN 1 END) as inactivos FROM medicos")->fetch();
    $totalMedicos = $medicosStat['activos'] . " / " . $medicosStat['inactivos'];
    
    $totalRecetas = $pdo->query("SELECT COUNT(*) FROM prescripciones")->fetchColumn();

    // Datos para el gráfico de coberturas (Dinámico)
    $obraStats = $pdo->query("SELECT o.nombre_obra, COUNT(p.id) as total 
                              FROM obras_sociales o 
                              LEFT JOIN pacientes p ON p.id_obra_social = o.id AND p.activo = 1 
                              GROUP BY o.id")->fetchAll();
    
    $chartData = [];
    $accum = 0;
    foreach($obraStats as $stat) {
        $percent = ($totalPacientes > 0) ? ($stat['total'] / $totalPacientes) * 100 : 0;
        $chartData[] = ['name' => $stat['nombre_obra'], 'percent' => $percent];
    }
} catch (Exception $e) {
    $error_db = "Error al cargar datos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SaludWEB Pro</title>
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; --text: #1e293b; --danger: #e11d48; --warning: #f59e0b; --success: #10b981; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .dashboard { max-width: 1200px; margin: 0 auto; }

        /* Header con Configuración a la derecha */
        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .config-link { text-decoration: none; color: var(--primary); font-weight: 800; font-size: 14px; display: flex; align-items: center; gap: 8px; transition: 0.3s; padding: 10px 20px; border-radius: 12px; background: white; border: 2px solid var(--primary); box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15); }
        .config-link:hover { background: var(--primary); color: white; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(79, 70, 229, 0.3); }

        /* Logo Estilizado */
        .logo-container { display: flex; align-items: center; gap: 10px; }
        .logo-box { background: var(--primary); color: white; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 24px; font-weight: bold; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        .logo-text { font-size: 28px; font-weight: 800; letter-spacing: -1px; }
        .logo-pro { background: #fee2e2; color: var(--danger); padding: 2px 8px; border-radius: 6px; font-size: 14px; vertical-align: middle; margin-left: 5px; }
        
        /* Menú con 9 carpetas de colores */
        .grid-menu { display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 12px; margin-bottom: 25px; }
        .menu-item { background: white; padding: 18px 10px; border-radius: 15px; text-align: center; border: 2px solid #e2e8f0; font-size: 10px; font-weight: 800; transition: 0.3s; text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 8px; text-transform: uppercase; line-height: 1.2; }
        .menu-item:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); border-color: transparent; color: white !important; }
        .menu-item i { display: block; font-size: 20px; margin-bottom: 5px; }

        /* Tarjetas de métricas */
        .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-bottom: 25px; }
        .stat-card { background: white; padding: 15px; border-radius: 12px; border-left: 5px solid #cbd5e1; border-top: 1px solid #f1f5f9; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .stat-card span { font-size: 22px; font-weight: 800; display: block; margin-top: 4px; }
        .stat-label { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: bold; }

        /* Buscador Remarcado */
        .search-bar { margin-bottom: 30px; display: flex; gap: 12px; }
        .search-bar input { 
            flex: 1; padding: 16px; border-radius: 15px; border: 3px solid #cbd5e1; outline: none; font-size: 16px; font-weight: 600; 
            transition: 0.3s; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
        }
        .search-bar input:focus { border-color: var(--primary); box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.15); }
        .search-bar button { 
            padding: 0 25px; border-radius: 10px; border: none; background: var(--primary); color: white; font-weight: 800; cursor: pointer; 
            display: flex; align-items: center; gap: 10px; transition: 0.3s; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4); font-size: 14px;
        }
        .search-bar button:hover { background: #3730a3; transform: scale(1.05); box-shadow: 0 6px 15px rgba(79, 70, 229, 0.5); }

        .main-layout { display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start; }
        .box { background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; }

        /* Tabla con Badges de colores */
        table { width: 100%; border-collapse: collapse; }
        th { color: #64748b; font-size: 12px; text-transform: uppercase; padding: 12px; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; }
        .badge { padding: 4px 10px; border-radius: 999px; color: white; font-size: 10px; font-weight: bold; }
        .acciones { display: flex; gap: 8px; justify-content: center; }
        .acciones a { text-decoration: none; transition: transform 0.2s; }
        .acciones a:hover { transform: scale(1.3); }

        .alert { padding: 15px; background: #d1fae5; color: #065f46; border-radius: 10px; margin-bottom: 20px; border-left: 5px solid #10b981; }
        
        /* Gráfico circular colorido */
        <?php
            // Generamos el gradiente dinámico
            $grad = "conic-gradient(";
            $last = 0;
            $colors = ['var(--primary)', 'var(--success)', 'var(--danger)', 'var(--warning)'];
            foreach($chartData as $i => $cd) {
                $next = $last + $cd['percent'];
                $grad .= $colors[$i % 4] . " $last% $next%, ";
                $last = $next;
            }
            $grad = rtrim($grad, ", ") . ")";
        ?>
        .chart { width: 150px; height: 150px; border-radius: 50%; margin: 20px auto; background: <?= $grad ?>; }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="header-top">
        <div class="logo-container">
            <div class="logo-box">✚</div>
            <div class="logo-text">SaludWeb<span class="logo-pro">PRO</span></div>
        </div>
        <a href="configuracion" class="config-link">⚙️ Configuración</a>
    </div>

    <?php if(isset($error_db)): ?>
        <div style="background:#fee2e2; color:#b91c1c; padding:15px; border-radius:10px; border-left:5px solid #ef4444; margin-bottom:20px;"><b>Error de Sistema:</b> <?= $error_db ?></div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="alert"><?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
    <?php endif; ?>

    <div class="grid-menu">
        <?php 
        $menu = [
            ['l' => 'Registrar Nuevo paciente', 'u' => 'registro', 'c' => '#4f46e5', 'i' => '👤'],
            ['l' => 'MRx Digital', 'u' => 'nueva-receta', 'c' => '#10b981', 'i' => '💊'],
            ['l' => 'Ver Recetas', 'u' => 'prescripciones', 'c' => '#3b82f6', 'i' => '📄'],
            ['l' => 'Módulos Farmacia', 'u' => 'farmacia', 'c' => '#f59e0b', 'i' => '🏥'],
            ['l' => 'Gestión Médicos', 'u' => 'medicos', 'c' => '#ec4899', 'i' => '👨‍⚕️'],
            ['l' => 'Asistente AI', 'u' => 'chat', 'c' => '#8b5cf6', 'i' => '🤖'],
            ['l' => 'Escritorio', 'u' => 'escritorio', 'c' => '#06b6d4', 'i' => '🖥️'],
            ['l' => 'API Docs', 'u' => 'api-docs', 'c' => '#64748b', 'i' => '📚'],
            ['l' => 'Ver Papelera', 'u' => 'papelera', 'c' => '#ef4444', 'i' => '🗑️'],
        ];
        foreach($menu as $m): ?>
            <a href="<?= $m['u'] ?>" class="menu-item" style="border-bottom: 5px solid <?= $m['c'] ?>; color: <?= $m['c'] ?>;" onmouseover="this.style.background='<?= $m['c'] ?>'" onmouseout="this.style.background='white'">
                <span style="font-size: 24px;"><?= $m['i'] ?></span>
                <?= $m['l'] ?>
            </a>
        <?php endforeach; ?>
    </div>

    <form class="search-bar" method="GET" action="lista">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por nombre o DNI de paciente...">
        <button type="submit" translate="no" class="notranslate">BUSCAR</button>
        <?php if($search): ?><a href="lista" style="padding:12px; color:var(--danger)">Limpiar</a><?php endif; ?>
    </form>

    <div class="grid-stats">
        <div class="stat-card" style="border-left-color:#3b82f6"><div class="stat-label">Total Pacientes</div><span><?=$totalPacientes?></span></div>
        <div class="stat-card" style="border-left-color:#f97316"><div class="stat-label">Pendiente Triage</div><span><?=$pendientesTriage?></span></div>
        <div class="stat-card" style="border-left-color:var(--danger)"><div class="stat-label">Casos Críticos</div><span><?=$criticos?></span></div>
        <div class="stat-card" style="border-left-color:var(--warning)"><div class="stat-label">Dolor Promedio</div><span><?=$promedioDolor?></span></div>
        <div class="stat-card" style="border-left-color:#a855f7"><div class="stat-label">Médicos (Act/Ina)</div><span><?=$totalMedicos?></span></div>
        <div class="stat-card" style="border-left-color:var(--success)"><div class="stat-label">Recetas</div><span><?=$totalRecetas?></span></div>
    </div>

    <?php if (!empty($pacientes)): ?>
    <div class="main-layout">
        <div class="box">
            <h3>Lista de Atención</h3>
            <table>
                <thead><tr><th>Estado</th><th>Pacientes</th><th>DNI</th><th>ESTADO ACTIVO</th><th>OBRA SOCIAL</th><th>ACCIONES</th></tr></thead>
                <tbody>
                    <?php foreach ($pacientes as $p): 
                        $lvl = $p['nivel_gravedad'] ?? 0;
                        $color = ($lvl >= 8) ? 'var(--danger)' : (($lvl >= 5) ? 'var(--warning)' : 'var(--success)');
                        $txt = ($lvl >= 8) ? 'EMERGENCIA' : (($lvl >= 5) ? 'URGENTE' : 'ESTABLE');
                    ?>
                    <tr>
                        <td><span class="badge" style="background:<?=$color?>"><?=$txt?></span></td>
                        <td><?=htmlspecialchars($p['nombre'] ?? 'Sin nombre')?></td>
                        <td><?=htmlspecialchars($p['dni'] ?? 'N/A')?></td>
                        <td><span class="badge" style="background:var(--success)">ACTIVO</span></td>
                        <td><?=htmlspecialchars($p['nombre_obra'] ?? 'Particular')?></td>
                        <td class="acciones">
                            <a href="ver-triage?id=<?=$p['id']?>" title="Ver Expediente">👁️</a>
                            <a href="triage?id=<?=$p['id']?>" title="Nuevo Triage">📁</a>
                            <a href="nueva-receta?id_paciente=<?=$p['id']?>" title="Nueva Receta">💊</a>
                            <a href="auditoria?id_paciente=<?=$p['id']?>" title="Consultas/Auditoría">📒</a>
                            <a href="editar?id=<?=$p['id']?>" title="Editar">✏️</a>
                            <a href="eliminar?id=<?=$p['id']?>" title="Eliminar" onclick="return confirm('¿Seguro?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="box">
            <h3>Coberturas</h3>
            <div class="chart"></div>
            <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px; font-size: 15px; font-weight: 800;">
                <?php 
                $colors = ['var(--primary)', 'var(--success)', 'var(--danger)', 'var(--warning)'];
                foreach($chartData as $i => $cd) {
                    echo "<div style='color:{$colors[$i % 4]}; padding: 12px; border-radius: 12px; background: white; border: 3px solid {$colors[$i % 4]}; box-shadow: 0 4px 10px rgba(0,0,0,0.08); text-align: center; text-transform: uppercase; letter-spacing: 0.5px;'>● " . htmlspecialchars($cd['name']) . "</div>";
                }
                ?>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="box" style="text-align:center; padding:40px;">
            <h3>No hay pacientes para mostrar.</h3>
        </div>
    <?php endif; ?>
</div>
</body>
</html>