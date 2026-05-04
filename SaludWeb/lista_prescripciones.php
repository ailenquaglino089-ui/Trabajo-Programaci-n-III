<?php
// SaludWeb/lista_prescripciones.php
require_once 'db.php';

$estado = $_GET['estado'] ?? 'activa';
$sql = "SELECT p.*, pac.nombre AS paciente_nombre, m.nombre AS medico_nombre
        FROM prescripciones p
        LEFT JOIN pacientes pac ON p.id_paciente = pac.id
        LEFT JOIN medicos m ON p.id_medico = m.id
        WHERE p.estado = ?
        ORDER BY p.fecha_emision DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$estado]);
$prescripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <th>QR Code</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prescripciones as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo $p['paciente_nombre']; ?></td>
                    <td><?php echo $p['medico_nombre']; ?></td>
                    <td><?php echo $p['fecha_emision']; ?></td>
                    <td><?php echo $p['fecha_vencimiento']; ?></td>
                    <td><?php echo $p['medicamentos']; ?></td>
                    <td><?php echo $p['indicaciones']; ?></td>
                    <td class="estado-<?php echo $p['estado']; ?>"><?php echo ucfirst($p['estado']); ?></td>
                    <td><?php echo $p['qr_code']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top:25px; padding:18px; background:#f7f7f7; border-left:4px solid #007bff; color:#333; font-size:0.95rem; line-height:1.5;">
        <strong>Información importante:</strong> Esta receta fue creada por un emisor inscripto y validado en el Registro de Recetarios Electrónicos del Ministerio de Salud de la Nación.<br>
        <em>Resolución RL-2024-91317760-APN-SSVEIYES#MS</em>
    </div>
</body>
</html>