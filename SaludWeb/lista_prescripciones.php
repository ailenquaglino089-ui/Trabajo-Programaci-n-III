<?php
// SaludWeb/lista_prescripciones.php
require_once 'db.php';

$estado = $_GET['estado'] ?? 'activa';
$paciente_id = $_GET['paciente_id'] ?? null;

$sql = "SELECT p.*, pac.nombre AS paciente_nombre, m.nombre AS medico_nombre
        FROM prescripciones p
        LEFT JOIN pacientes pac ON p.id_paciente = pac.id
        LEFT JOIN medicos m ON p.id_medico = m.id
        WHERE p.estado = ?";

if ($paciente_id) { $sql .= " AND p.id_paciente = " . (int)$paciente_id; }
$sql .= " ORDER BY p.fecha_emision DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$estado]);
$prescripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtenemos un mapa de medicamentos [id => nombre] para traducir los IDs del JSON a nombres legibles
$medsQuery = $pdo->query("SELECT id, nombre FROM medicamentos");
$mapaMedicamentos = $medsQuery->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Prescripciones - MRx Digital</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding-bottom: 40px; }
        .container { max-width: 1200px; margin: auto; padding: 0 12px; }
        h1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 24px 28px; border-radius: 16px; box-shadow: 0 12px 30px rgba(102, 126, 234, 0.3); text-align: center; font-size: 2rem; letter-spacing: 0.5px; margin-bottom: 24px; text-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        
        form { background: white; padding: 20px; border-radius: 12px; margin-bottom: 24px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); border-top: 4px solid #667eea; }
        form label { font-weight: 700; color: #1a202c; display: inline-block; margin-right: 16px; font-size: 1rem; }
        form select { padding: 10px 14px; border: 2px solid #667eea; border-radius: 8px; font-size: 0.95rem; cursor: pointer; background: white; color: #1a202c; font-weight: 600; transition: 0.3s; }
        form select:hover { background: #f9fafb; border-color: #764ba2; }
        form select:focus { outline: none; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 0; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 12px 30px rgba(0,0,0,0.1); }
        th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 16px 12px; text-align: left; font-weight: 700; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px; }
        td { border-bottom: 1px solid #e5e7eb; padding: 14px 12px; text-align: left; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f9fafb; }
        
        .estado-activa { background: #dcfce7; color: #15803d; font-weight: 700; padding: 6px 12px; border-radius: 8px; display: inline-block; border: 2px solid #15803d; }
        .estado-dispensada { background: #dbeafe; color: #0c4a6e; font-weight: 700; padding: 6px 12px; border-radius: 8px; display: inline-block; border: 2px solid #0c4a6e; }
        .estado-vencida { background: #fee2e2; color: #7f1d1d; font-weight: 700; padding: 6px 12px; border-radius: 8px; display: inline-block; border: 2px solid #7f1d1d; }
        .estado-cancelada { background: #f3f4f6; color: #4b5563; font-weight: 700; padding: 6px 12px; border-radius: 8px; display: inline-block; border: 2px solid #4b5563; }
        
        button { cursor: pointer; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; padding: 10px 14px; border-radius: 8px; font-weight: 700; transition: 0.3s; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2); font-size: 0.9rem; width: 100%; max-width: 160px; }
        button:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(239, 68, 68, 0.3); }
        
        .info-box { margin-top: 28px; padding: 22px 24px; background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%); border-left: 6px solid #dc2626; border-radius: 12px; color: white; font-size: 0.95rem; line-height: 1.6; box-shadow: 0 8px 20px rgba(249, 115, 22, 0.2); font-weight: 500; }
        .info-box strong { font-size: 1.1rem; display: block; margin-bottom: 8px; text-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        .info-box em { display: block; margin-top: 12px; font-style: normal; font-weight: 700; letter-spacing: 0.5px; }
        
        @media (max-width: 840px) {
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { position: absolute; top: -9999px; left: -9999px; }
            tr { margin-bottom: 18px; border: 2px solid #667eea; border-radius: 12px; overflow: hidden; background: white; }
            td { border: none; padding: 12px 14px; position: relative; }
            td::before { content: attr(data-label); font-weight: 700; display: block; margin-bottom: 8px; color: #667eea; }
            td button { width: 100%; }
        }
        @media (max-width: 640px) {
            body { margin: 10px; }
            h1 { font-size: 1.5rem; padding: 16px 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
    <h1>📋 MRx Digital - Lista de Recetas Electrónicas</h1>
    <form method="GET">
        <label for="estado">🔍 Filtrar por Estado:</label>
        <select name="estado" id="estado" onchange="this.form.submit()">
            <option value="activa" <?php if ($estado == 'activa') echo 'selected'; ?>>✓ Activa</option>
            <option value="dispensada" <?php if ($estado == 'dispensada') echo 'selected'; ?>>✔️ Dispensada</option>
            <option value="vencida" <?php if ($estado == 'vencida') echo 'selected'; ?>>⏰ Vencida</option>
            <option value="cancelada" <?php if ($estado == 'cancelada') echo 'selected'; ?>>✗ Cancelada</option>
        </select>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Fecha Emisión</th>
                <th>Fecha Vencimiento</th>
                <th>Medicamentos</th>
                <th>Indicaciones</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-recetas">
            <?php foreach ($prescripciones as $p): ?>
                <tr id="fila-<?php echo $p['id']; ?>">
                    <td data-label="ID"><?php echo $p['id']; ?></td>
                    <td data-label="Paciente"><?php echo $p['paciente_nombre']; ?></td>
                    <td data-label="Médico"><?php echo $p['medico_nombre']; ?></td>
                    <td data-label="Fecha Emisión"><?php echo $p['fecha_emision']; ?></td>
                    <td data-label="Fecha Vencimiento"><?php echo $p['fecha_vencimiento']; ?></td>
                    <td data-label="Medicamentos">
                        <?php 
                        $items = json_decode($p['medicamentos'], true);
                        if (is_array($items)) {
                            foreach ($items as $item) {
                                $nombreMed = $mapaMedicamentos[$item['id']] ?? "Medicamento desconocido";
                                echo "• <strong>" . htmlspecialchars($nombreMed) . "</strong>: " . htmlspecialchars($item['dosis']) . "<br>";
                            }
                        } else {
                            echo "Sin datos";
                        }
                        ?>
                    </td>
                    <td data-label="Indicaciones"><?php echo $p['indicaciones']; ?></td>
                    <td data-label="Estado" class="estado-<?php echo $p['estado']; ?>"><?php echo ucfirst($p['estado']); ?></td>
                    <td data-label="Acciones">
                        <button onclick="eliminarReceta(<?php echo $p['id']; ?>)">
                            🗑️ Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="info-box">
        <strong>✓ Información Importante - Receta Validada</strong>
        Esta receta fue creada por un emisor inscripto y validado en el Registro de Recetarios Electrónicos del Ministerio de Salud de la Nación.
        <em>📌 Resolución RL-2024-91317760-APN-SSVEIYES#MS</em>
    </div>
    </div>

    <script>
    async function eliminarReceta(id) {
        if (!confirm('¿Estás seguro de que deseas eliminar permanentemente esta receta?')) return;

        const apiUrl = '/prog3-clase2/SaludWeb/api/prescripciones.php?id=' + id;

        try {
            const response = await fetch(apiUrl, { method: 'DELETE' });
            const result = await response.json();

            if (response.ok) {
                alert(result.message);
                document.getElementById('fila-' + id).remove();
            } else {
                alert('Error: ' + (result.error || 'No se pudo eliminar'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión con la API');
        }
    }
    </script>
</body>
</html>