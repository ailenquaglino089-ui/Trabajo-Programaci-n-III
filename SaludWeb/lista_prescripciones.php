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
    <title>Lista de Prescripciones - MRx Digital</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .estado-activa { color: green; }
        .estado-dispensada { color: blue; }
        .estado-vencida { color: red; }
        .estado-cancelada { color: gray; }
    </style>
</head>
<body>
    <h1>MRx Digital - Lista de Recetas Electrónicas</h1>
    <form method="GET">
        <label for="estado">Filtrar por Estado:</label>
        <select name="estado" id="estado" onchange="this.form.submit()">
            <option value="activa" <?php if ($estado == 'activa') echo 'selected'; ?>>Activa</option>
            <option value="dispensada" <?php if ($estado == 'dispensada') echo 'selected'; ?>>Dispensada</option>
            <option value="vencida" <?php if ($estado == 'vencida') echo 'selected'; ?>>Vencida</option>
            <option value="cancelada" <?php if ($estado == 'cancelada') echo 'selected'; ?>>Cancelada</option>
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
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo $p['paciente_nombre']; ?></td>
                    <td><?php echo $p['medico_nombre']; ?></td>
                    <td><?php echo $p['fecha_emision']; ?></td>
                    <td><?php echo $p['fecha_vencimiento']; ?></td>
                    <td>
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
                    <td><?php echo $p['indicaciones']; ?></td>
                    <td class="estado-<?php echo $p['estado']; ?>"><?php echo ucfirst($p['estado']); ?></td>
                    <td>
                        <button onclick="eliminarReceta(<?php echo $p['id']; ?>)" style="background:#ff4d4d; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;">
                            🗑️ Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top:25px; padding:18px; background:#f7f7f7; border-left:4px solid #007bff; color:#333; font-size:0.95rem; line-height:1.5;">
        <strong>Información importante:</strong> Esta receta fue creada por un emisor inscripto y validado en el Registro de Recetarios Electrónicos del Ministerio de Salud de la Nación.<br>
        <em>Resolución RL-2024-91317760-APN-SSVEIYES#MS</em>
    </div>

    <script>
    async function eliminarReceta(id) {
        if (!confirm('¿Estás seguro de que deseas eliminar permanentemente esta receta?')) return;

        const apiUrl = window.location.origin + '/prog3-clase2/api/prescripciones/' + id;

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