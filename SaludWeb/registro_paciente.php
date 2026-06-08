<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$error = null;
$success = null;
$pacientes_db = [];

try {
    require_once __DIR__ . '/db.php';

    // Cargar pacientes para el autocompletado
    $stmt = $pdo->query("SELECT * FROM pacientes WHERE activo = 1 ORDER BY nombre ASC");
    $pacientes_db = $stmt->fetchAll();
} catch (Exception $e) {
    $error = 'Error al acceder a la base de datos: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SaludWEB</title>
    <style>
        body { margin: 0; font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: linear-gradient(135deg, #f0f4f8 0%, #f9fafb 100%); display: flex; justify-content: center; padding: 40px 20px; }
        .card { background: white; border-radius: 20px; box-shadow: 0 16px 50px rgba(0,0,0,0.08); width: 100%; max-width: 520px; overflow: hidden; }
        h2 { margin: 0; padding: 20px; background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); color: white; font-size: 1.5rem; text-align: center; letter-spacing: 0.5px; }
        .search-section { background: #ecf0ff; border-left: 5px solid #2563eb; padding: 24px 20px; margin: 20px; border-radius: 14px; }
        .search-section label { display: block; margin-bottom: 10px; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.4px; font-size: 0.85rem; }
        .search-section select { width: 100%; padding: 14px; border: 2px solid #93c5fd; border-radius: 10px; background: white; font-size: 0.95rem; color: #1e293b; font-weight: 600; cursor: pointer; }
        .search-section select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
        .form-section { padding: 24px 20px; margin: 0 20px 20px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 14px; }
        .form-section h3 { margin: 0 0 20px 0; color: #0f172a; font-size: 1.1rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.3px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.4px; font-size: 0.85rem; }
        .form-group input, .form-group select { width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 10px; background: white; font-size: 0.95rem; color: #0f172a; }
        .form-group input::placeholder { color: #94a3b8; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        button { width: 100%; padding: 14px 16px; margin: 0 20px 20px; background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; border: none; border-radius: 12px; cursor: pointer; font-weight: 800; letter-spacing: 0.3px; font-size: 0.95rem; box-shadow: 0 12px 24px rgba(16, 185, 129, 0.25); }
        button:hover { background: linear-gradient(135deg, #059669 0%, #10b981 100%); transform: translateY(-1px); }
        .link-back { display: block; margin: 16px 20px 20px; text-align: center; color: #2563eb; text-decoration: none; font-weight: 700; }
        .link-back:hover { text-decoration: underline; }
        @media (max-width: 640px) {
            body { padding: 20px 16px; }
            .card { border-radius: 16px; }
            h2 { font-size: 1.3rem; padding: 16px; }
            .search-section, .form-section { padding: 16px; margin: 0 12px 16px; }
            button { margin: 0 12px 16px; }
            .link-back { margin: 12px; }
        }
    </style>
</head>
<body>
<div class="card">
    <h2>SaludWEB: Registro de Paciente Nuevo</h2>
    <?php if (!empty($error)): ?>
        <div style="background:#fee2e2; color:#9b1c1c; padding:14px; margin:16px; border-radius:10px; border-left:4px solid #dc2626;">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div style="background:#dcfce7; color:#166534; padding:14px; margin:16px; border-radius:10px; border-left:4px solid #22c55e;">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($_GET['mensaje'])): ?>
        <div style="background:#dcfce7; color:#166534; padding:14px; margin:16px; border-radius:10px; border-left:4px solid #22c55e;">
            <?php echo htmlspecialchars($_GET['mensaje']); ?>
        </div>
    <?php endif; ?>

    <div class="search-section">
        <label>🔍 Buscar Paciente Existente:</label>
        <select onchange="autoLlenar(this)">
            <option value="">-- Seleccionar --</option>
            <?php foreach ($pacientes_db as $p): ?>
                <option value="<?php echo $p['id']; ?>" data-dni="<?php echo $p['dni']; ?>" data-nom="<?php echo $p['nombre']; ?>" data-obra="<?php echo $p['id_obra_social']; ?>">
                    <?php echo $p['nombre']; ?> (<?php echo $p['dni']; ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-section">
        <h3>📝 Registrar Nuevo Paciente</h3>
        <form action="procesar_registro.php" method="POST">
            <div class="form-group">
                <label for="dn">DNI del Paciente:</label>
                <input type="text" name="dni" id="dn" placeholder="Ej. 12345678" required>
            </div>
            <div class="form-group">
                <label for="nom">Nombre Completo:</label>
                <input type="text" name="nombre" id="nom" placeholder="Ej. Juan Pérez" required>
            </div>
            <div class="form-group">
                <label for="obr">Obra Social:</label>
                <select name="id_obra_social" id="obr">
                    <option value="1">Particular</option>
                    <option value="2">OSDE</option>
                    <option value="3">PAMI</option>
                </select>
            </div>
            <button type="submit">Guardar y Continuar al Triage</button>
        </form>
    </div>

    <p style="text-align:center; margin: 0 20px 24px;"><a href="lista" class="link-back">← Volver al Dashboard</a></p>
</div>

<script>
function autoLlenar(sel) {
    const opt = sel.options[sel.selectedIndex];
    if(opt.value !== "") {
        document.getElementById('dn').value = opt.getAttribute('data-dni');
        document.getElementById('nom').value = opt.getAttribute('data-nom');
        document.getElementById('obr').value = opt.getAttribute('data-obra');
    }
}
</script>
</body>
</html>
