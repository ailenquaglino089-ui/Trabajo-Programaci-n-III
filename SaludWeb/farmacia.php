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
    <title>Farmacia - SaludWEB</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; padding: 30px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 1000px; margin: auto; }
        h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        .search { margin-bottom: 25px; padding: 15px; background: #eef2f3; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #3498db; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .btn { background: #27ae60; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; }
        .btn:hover { background: #219150; }
        .badge { background: #d35400; color: white; padding: 3px 8px; border-radius: 10px; font-size: 0.8em; }
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
                <a href="farmacia">Ver Todas</a>
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
                    <td><strong><?php echo htmlspecialchars($p['paciente_nombre']); ?></strong><br><small>DNI: <?php echo $p['paciente_dni']; ?></small></td>
                    <td><?php echo htmlspecialchars($p['medico_nombre']); ?></td>
                    <td>
                        <?php 
                        $meds = json_decode($p['medicamentos'], true);
                        foreach ($meds as $m) {
                            $nombre = $nombresMedicamentos[$m['id']] ?? "Desconocido";
                            echo "• " . htmlspecialchars($nombre) . " <span class='badge'>" . htmlspecialchars($m['dosis']) . "</span><br>";
                        }
                        ?>
                    </td>
                    <td>
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