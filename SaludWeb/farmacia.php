<?php
// SaludWeb/farmacia.php
require_once __DIR__ . '/db.php';

$dniFiltro = $_GET['dni'] ?? '';
$prescripciones = [];

try {
    // Mapeo de nombres de medicamentos
    $medStmt = $pdo->query("SELECT id, nombre FROM medicamentos");
    $nombresMedicamentos = $medStmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Consulta unificada
    $sql = "SELECT pr.*, p.nombre AS paciente_nombre, p.dni AS paciente_dni, m.nombre AS medico_nombre 
            FROM prescripciones pr
            JOIN pacientes p ON pr.id_paciente = p.id
            JOIN medicos m ON pr.id_medico = m.id
            WHERE pr.estado = 'activa'";
    
    if (!empty($dniFiltro)) {
        $sql .= " AND p.dni = :dni";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':dni' => $dniFiltro]);
    } else {
        $stmt = $pdo->query($sql);
    }
    $prescripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmacia - SaludWEB</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; padding: 20px; }
        .card { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.06); max-width: 1000px; margin: auto; }
        h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        .search { margin-bottom: 25px; padding: 15px; background: #eef2f3; border-radius: 12px; }
        .search label { display: block; margin-bottom: 8px; font-weight: 700; }
        .search input { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 12px; margin-bottom: 12px; }
        .search button, .search a { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 10px; text-decoration: none; font-weight: 700; }
        .search button { background: #3498db; color: white; border: none; cursor: pointer; }
        .search a { background: #f8fafc; color: #2563eb; border: 1px solid #cbd5e1; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #3498db; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: top; }
        .btn { background: #27ae60; color: white; padding: 8px 15px; border-radius: 10px; text-decoration: none; font-weight: bold; display: inline-block; }
        .btn:hover { background: #219150; }
        .badge { background: #d35400; color: white; padding: 4px 10px; border-radius: 999px; font-size: 0.78rem; display: inline-block; margin-top: 4px; }
        @media (max-width: 900px) {
            body { padding: 16px; }
            .card { padding: 18px; }
            .search { padding: 12px; }
            .search button, .search a { width: 100%; justify-content: center; }
        }
        @media (max-width: 680px) {
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { position: absolute; top: -9999px; left: -9999px; }
            tr { margin-bottom: 16px; border-bottom: 1px solid #e5e7eb; }
            td { border: none; padding: 10px 0; position: relative; }
            td::before { content: attr(data-label); font-weight: 700; display: block; margin-bottom: 6px; }
            td:last-child { padding-bottom: 0; }
            .search input { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>💊 Dispensación Farmacéutica</h1>
        
        <?php if (isset($_GET['exito'])): ?>
            <div style="background:#d4edda; color:#155724; padding:10px; margin-bottom:15px; border-radius:5px;">✅ Medicación entregada con éxito.</div>
        <?php endif; ?>

        <div class="search">
            <form method="GET">
                <label>Buscar Paciente (DNI):</label>
                <input type="text" name="dni" value="<?php echo htmlspecialchars($dniFiltro); ?>" placeholder="Ingrese DNI...">
                <button type="submit">Validar</button>
                <a href="farmacia.php">Ver Todas</a>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Detalle de Receta</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescripciones as $p): ?>
                <tr>
                    <td data-label="Paciente"><strong><?php echo htmlspecialchars($p['paciente_nombre']); ?></strong><br><small>DNI: <?php echo $p['paciente_dni']; ?></small></td>
                    <td data-label="Médico"><?php echo htmlspecialchars($p['medico_nombre']); ?></td>
                    <td data-label="Detalle de Receta">
                        <?php 
                        $meds = json_decode($p['medicamentos'], true);
                        foreach ($meds as $m) {
                            $nombre = $nombresMedicamentos[$m['id']] ?? "Desconocido";
                            echo "• " . htmlspecialchars($nombre) . " <span class='badge'>" . htmlspecialchars($m['dosis']) . "</span><br>";
                        }
                        ?>
                    </td>
                    <td data-label="Acción">
                        <a href="procesar_dispensa?id=<?php echo $p['id']; ?>" class="btn" onclick="return confirm('¿Confirma que entrega los medicamentos al paciente?')">Dispensar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <a href="lista_pacientes.php" style="color:#7f8c8d;">← Volver al inicio</a>
    </div>
</body>
</html>