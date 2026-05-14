<?php
// SaludWeb/farmacia.php
require_once __DIR__ . '/db.php';

$dniFiltro = $_GET['dni'] ?? '';
$prescripciones = [];
$error = null;

try {
    // Obtenemos los nombres de los medicamentos para mostrar texto en lugar de IDs
    $medStmt = $pdo->query("SELECT id, nombre FROM medicamentos");
    $nombresMedicamentos = $medStmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Consulta para obtener recetas activas con datos de paciente y médico
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
    $error = "Error de conexión: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Farmacia - SaludWEB</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #1a73e8; border-bottom: 2px solid #e8f0fe; padding-bottom: 10px; }
        .search-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #dee2e6; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f3f4; padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .btn-dispensar { background: #28a745; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; }
        .medicamento-item { font-size: 0.9rem; margin-bottom: 4px; display: block; }
        .mensaje { padding: 15px; border-radius: 5px; margin-bottom: 20px; background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <div class="container">
        <h1>💊 Módulo de Farmacia: Entrega de Medicamentos</h1>

        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje">✅ <?php echo htmlspecialchars($_GET['mensaje']); ?></div>
        <?php endif; ?>

        <div class="search-box">
            <form method="GET" action="farmacia">
                <strong>Validar Receta:</strong> 
                <input type="text" name="dni" value="<?php echo htmlspecialchars($dniFiltro); ?>" placeholder="DNI del Paciente..." style="padding: 8px; width: 200px;">
                <button type="submit" style="padding: 8px 15px; cursor:pointer;">Buscar</button>
                <a href="farmacia" style="margin-left:10px; color:#666;">Limpiar</a>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Paciente (DNI)</th>
                    <th>Médico</th>
                    <th>Prescripción</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescripciones as $p): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($p['paciente_nombre']); ?></strong><br><?php echo htmlspecialchars($p['paciente_dni']); ?></td>
                    <td><?php echo htmlspecialchars($p['medico_nombre']); ?></td>
                    <td>
                        <?php 
                        $meds = json_decode($p['medicamentos'], true);
                        foreach ($meds as $m) {
                            $nombre = $nombresMedicamentos[$m['id']] ?? "ID: " . $m['id'];
                            echo "<span class='medicamento-item'>• " . htmlspecialchars($nombre) . " - <em>" . htmlspecialchars($m['dosis']) . "</em></span>";
                        }
                        ?>
                    </td>
                    <td>
                        <a href="procesar_dispensa?id=<?php echo $p['id']; ?>" class="btn-dispensar" onclick="return confirm('¿Confirmar entrega de medicación?')">Entregar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <a href="lista">← Volver al Dashboard</a>
    </div>
</body>
</html>