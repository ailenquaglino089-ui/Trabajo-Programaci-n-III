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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .search-bar .button-secondary {
            padding: 0 22px; border-radius: 10px; border: none; background: #e2e8f0; color: #1f2937; font-weight: 800; cursor: pointer;
            transition: 0.3s; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08); font-size: 14px;
        }
        .search-bar .button-secondary:hover { background: #cbd5e1; }

        .modal { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); align-items: center; justify-content: center; padding: 20px; z-index: 1000; }
        .modal.show { display: flex; }
        .modal-content { width: 100%; max-width: 560px; background: white; border-radius: 18px; box-shadow: 0 20px 60px rgba(15, 23, 42, 0.2); padding: 28px; position: relative; }
        .modal-content h2 { margin-top: 0; margin-bottom: 20px; font-size: 1.5rem; color: var(--text); }
        .modal-close { position: absolute; top: 18px; right: 18px; border: none; background: transparent; font-size: 24px; cursor: pointer; color: #334155; }
        .modal-form label { display: block; margin-top: 16px; font-weight: 700; color: #334155; }
        .modal-form input, .modal-form select { width: 100%; padding: 12px 14px; margin-top: 8px; border: 1px solid #cbd5e1; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; }
        .modal-actions { margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px; }
        .modal-actions button { min-width: 110px; padding: 12px 20px; border-radius: 12px; border: none; cursor: pointer; font-weight: 800; }
        .modal-actions .btn-primary { background: var(--primary); color: white; }
        .modal-actions .btn-secondary { background: #f1f5f9; color: #334155; }

        .main-layout { display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start; }
        .box { background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; }
        .box h3 { margin: 0 0 18px 0; color: white; background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); padding: 16px 18px; border-radius: 12px; font-size: 1.2rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 6px 15px rgba(37, 99, 235, 0.25); }

        /* Tabla con Badges de colores */
        table { width: 100%; border-collapse: collapse; }
        th { color: white; font-size: 13px; text-transform: uppercase; padding: 16px; font-weight: 900; background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); border: none; letter-spacing: 0.6px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
        td { padding: 14px; border-bottom: 1px solid #f1f5f9; font-weight: 600; }
        .badge { padding: 6px 12px; border-radius: 999px; color: white; font-size: 11px; font-weight: 900; letter-spacing: 0.3px; }
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

        @media (max-width: 900px) {
            body { padding: 14px; }
            .header-top { flex-wrap: wrap; gap: 12px; }
            .config-link { width: 100%; justify-content: center; }
            .grid-menu { grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); }
            .main-layout { grid-template-columns: 1fr; }
            .box { padding: 16px; }
            .stats { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 640px) {
            .search-bar { flex-direction: column; }
            .search-bar button { width: 100%; }
            .search-bar a { width: 100%; }
            .stats { grid-template-columns: 1fr; }
            .menu-item { padding: 16px 10px; font-size: 11px; }
            th, td { padding: 10px; }
            table { font-size: 0.9rem; }
            .chart { width: 120px; height: 120px; }
            .badge { font-size: 0.72rem; }
            .acciones { flex-wrap: wrap; gap: 6px; }
            .alert { font-size: 0.95rem; }
        }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="header-top">
        <div class="logo-container">
            <div class="logo-box">✚</div>
            <div class="logo-text">SaludWeb<span class="logo-pro">PRO</span></div>
        </div>
        <div style="display:flex; gap:12px; align-items:center;">
            <a href="configuracion.php" class="config-link">⚙️ Configuración</a>
            <a href="logout.php" class="config-link" style="border-color:#ef4444; color:#ef4444;">🔒 Cerrar sesión</a>
        </div>
    </div>

    <?php if(isset($error_db)): ?>
        <div style="background:#fee2e2; color:#b91c1c; padding:15px; border-radius:10px; border-left:5px solid #ef4444; margin-bottom:20px;"><b>Error de Sistema:</b> <?= $error_db ?></div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="alert"><?= htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['mensaje'])): ?>
        <div class="alert"><?= htmlspecialchars($_GET['mensaje']); ?></div>
    <?php endif; ?>

    <div class="grid-menu">
        <?php 
        $menu = [
            ['l' => 'Registrar Nuevo paciente', 'u' => 'registro_paciente.php', 'c' => '#4f46e5', 'i' => '👤'],
            ['l' => 'MRx Digital', 'u' => 'emitir_prescripcion.php', 'c' => '#10b981', 'i' => '💊'],
            ['l' => 'Ver Recetas', 'u' => 'lista_prescripciones.php', 'c' => '#3b82f6', 'i' => '📄'],
            ['l' => 'Módulos Farmacia', 'u' => 'farmacia.php', 'c' => '#f59e0b', 'i' => '🏥'],
            ['l' => 'Gestión Médicos', 'u' => 'lista_medicos.php', 'c' => '#ec4899', 'i' => '👨‍⚕️'],
            ['l' => 'Asistente AI', 'u' => 'chat.php', 'c' => '#8b5cf6', 'i' => '🤖'],
            ['l' => 'Escritorio', 'u' => 'escritorio.php', 'c' => '#06b6d4', 'i' => '🖥️'],
            ['l' => 'API Docs', 'u' => 'api_docs.php', 'c' => '#64748b', 'i' => '📚'],
            ['l' => 'Ver Papelera', 'u' => 'papelera.php', 'c' => '#ef4444', 'i' => '🗑️'],
        ];
        foreach($menu as $m): ?>
            <a href="<?= $m['u'] ?>" class="menu-item" style="border-bottom: 5px solid <?= $m['c'] ?>; color: <?= $m['c'] ?>;" onmouseover="this.style.background='<?= $m['c'] ?>'" onmouseout="this.style.background='white'">
                <span style="font-size: 24px;"><?= $m['i'] ?></span>
                <?= $m['l'] ?>
            </a>
        <?php endforeach; ?>
    </div>

    <form class="search-bar" method="GET" action="lista_pacientes.php">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por nombre o DNI de paciente...">
        <button type="button" id="openModal" class="button-secondary">+ Nuevo paciente</button>
        <button type="submit" translate="no" class="notranslate">BUSCAR</button>
        <?php if($search): ?><a href="lista_pacientes.php" style="padding:12px; color:var(--danger)">Limpiar</a><?php endif; ?>
    </form>

    <div id="modal" class="modal" aria-hidden="true">
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <button type="button" id="closeModal" class="modal-close" aria-label="Cerrar modal">×</button>
            <h2 id="modalTitle">Registrar nuevo paciente</h2>
            <form class="modal-form" action="registro_paciente.php" method="post">
                <label for="modalNombre">Nombre completo</label>
                <input id="modalNombre" name="nombre" type="text" required>

                <label for="modalDni">DNI</label>
                <input id="modalDni" name="dni" type="text" required>

                <label for="modalObra">Obra social</label>
                <select id="modalObra" name="id_obra_social">
                    <option value="">Particular</option>
                    <option value="1">OSDE</option>
                    <option value="2">Swiss Medical</option>
                    <option value="3">Galeno</option>
                </select>

                <label for="modalTelefono">Teléfono</label>
                <input id="modalTelefono" name="telefono" type="text">

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="closeModalAlt">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

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
                        <td style="font-weight: 900; color: #1e40af; font-size: 15px;"><?=htmlspecialchars($p['nombre'] ?? 'Sin nombre')?></td>
                        <td style="font-weight: 800; color: #475569;"><?=htmlspecialchars($p['dni'] ?? 'N/A')?></td>
                        <td><span class="badge" style="background:var(--success)">ACTIVO</span></td>
                        <td style="font-weight: 700; color: #334155;"><?=htmlspecialchars($p['nombre_obra'] ?? 'Particular')?></td>
                        <td class="acciones">
                            <a href="ver_triage.php?id=<?=$p['id']?>" title="Ver Expediente">👁️</a>
                            <a href="triage.php?id=<?=$p['id']?>" title="Nuevo Triage">📁</a>
                            <a href="emitir_prescripcion.php?id_paciente=<?=$p['id']?>" title="Nueva Receta">💊</a>
                            <a href="auditoria.php?paciente_id=<?=$p['id']?>" title="Consultas/Auditoría">📒</a>
                            <a href="editar_paciente.php?id=<?=$p['id']?>" title="Editar">✏️</a>
                            <a href="eliminar_paciente.php?id=<?=$p['id']?>" title="Eliminar" onclick="return confirm('¿Seguro?')">🗑️</a>
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
<script>
    const modal = document.getElementById('modal');
    const openModal = document.getElementById('openModal');
    const closeModal = document.getElementById('closeModal');
    const closeModalAlt = document.getElementById('closeModalAlt');

    const toggleModal = (show) => {
        if (!modal) return;
        modal.classList.toggle('show', show);
        modal.setAttribute('aria-hidden', String(!show));
    };

    openModal?.addEventListener('click', () => toggleModal(true));
    closeModal?.addEventListener('click', () => toggleModal(false));
    closeModalAlt?.addEventListener('click', () => toggleModal(false));

    modal?.addEventListener('click', (event) => {
        if (event.target === modal) {
            toggleModal(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal?.classList.contains('show')) {
            toggleModal(false);
        }
    });
</script>
</body>
</html>