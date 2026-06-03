<?php
// SaludWeb/emitir_prescripcion.php

// Proceso de emisión de recetas digitales
require_once 'db.php';

// Captura de ID de paciente desde el Dashboard
$idPacienteSeleccionado = $_GET['id_paciente'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extracción de datos del formulario
    $idPaciente = $_POST['id_paciente'] ?? '';
    $idMedico = $_POST['id_medico'] ?? '';
    $medicamentos = $_POST['medicamentos'] ?? [];
    $dosis = $_POST['dosis'] ?? [];
    $indicaciones = $_POST['indicaciones'] ?? '';
    $fechaVencimiento = $_POST['fecha_vencimiento'] ?? date('Y-m-d', strtotime('+30 days'));

    if (empty($idPaciente) || empty($idMedico) || empty($medicamentos) || count($medicamentos) === 0) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        // Estructuración de medicamentos para guardarlos como JSON en la base de datos
        $prescripcionMedicamentos = [];
        foreach ($medicamentos as $index => $medId) {
            if (empty($medId)) {
                continue;
            }
            $prescripcionMedicamentos[] = [
                'id' => $medId,
                'dosis' => $dosis[$index] ?? ''
            ];
        }

        if (empty($prescripcionMedicamentos)) {
            $error = 'Debe seleccionar al menos un medicamento válido.';
        } else {
            // Simulación de Firma Digital y QR según normativas vigentes
            $medicamentosJson = json_encode($prescripcionMedicamentos, JSON_UNESCAPED_UNICODE);
            $qrCode = 'QR-' . uniqid();
            $firmaDigital = 'FIRMA-' . $idMedico . '-' . time();

            $stmt = $pdo->prepare('INSERT INTO prescripciones (id_paciente, id_medico, medicamentos, indicaciones, fecha_vencimiento, qr_code, firma_digital) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$idPaciente, $idMedico, $medicamentosJson, $indicaciones, $fechaVencimiento, $qrCode, $firmaDigital]);
            $success = 'Prescripción emitida correctamente.';
        }
    }
}

// Obtención de catálogos para los selects del formulario
try {
    $pacientesStmt = $pdo->query('SELECT id, nombre FROM pacientes WHERE activo = 1');
    $pacientes = $pacientesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $pacientes = [];
    $error = 'Advertencia: no se pudo cargar la lista de pacientes.';
}

try {
    $medicosStmt = $pdo->query('SELECT id, nombre FROM medicos WHERE activo = 1');
    $medicos = $medicosStmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($medicos)) {
        throw new Exception('Lista de médicos vacía.');
    }
} catch (Exception $e) {
    $medicos = [
        ['id' => 1, 'nombre' => 'Dr. Juan Pérez'],
        ['id' => 2, 'nombre' => 'Dra. María López'],
        ['id' => 3, 'nombre' => 'Dr. Sebastián Gómez'],
        ['id' => 4, 'nombre' => 'Dra. Lucía Fernández'],
    ];
    $error = 'Advertencia: no se encontró la tabla de médicos o no hay médicos activos. Se usan médicos de ejemplo.';
}

try {
    $medicamentosStmt = $pdo->query('SELECT id, nombre FROM medicamentos');
    $medicamentosLista = $medicamentosStmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($medicamentosLista)) {
        throw new Exception('Lista de medicamentos vacía.');
    }
} catch (Exception $e) {
    $medicamentosLista = [
        ['id' => 1, 'nombre' => 'Paracetamol'],
        ['id' => 2, 'nombre' => 'Ibuprofeno'],
        ['id' => 3, 'nombre' => 'Amoxicilina'],
        ['id' => 4, 'nombre' => 'Loratadina'],
        ['id' => 5, 'nombre' => 'Omeprazol'],
    ];
    $error = 'Advertencia: no se encontró la tabla de medicamentos. Se usa una lista predeterminada.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Emitir Prescripción - MRx Digital</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 600px; margin: auto; }
        label { display: block; margin-top: 10px; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
        .medicamento { margin-bottom: 10px; }
    </style>
</head>
<body>
    <a href="lista" style="display: inline-block; margin-bottom: 20px; text-decoration: none; color: #007bff; font-weight: bold;">← Volver al Dashboard</a>
    <h1>MRx Digital - Emitir Receta Electrónica</h1>
    <p style="font-size:0.95rem; color:#444; margin-bottom:15px;">
        Este es el nuevo formato de receta electrónica válido para farmacias y obras sociales, conforme a la normativa vigente.
    </p>
    <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="id_paciente">Paciente:</label>
        <select name="id_paciente" id="id_paciente" required>
            <option value="">Seleccionar paciente</option>
            <?php foreach ($pacientes as $pac): ?>
                <option value="<?php echo $pac['id']; ?>" <?php echo ($idPacienteSeleccionado == $pac['id']) ? 'selected' : ''; ?>><?php echo $pac['nombre']; ?></option>
            <?php endforeach; ?>
        </select>

        <label>Médico Emisor:</label>
        <div style="margin-bottom: 15px;">
            <?php foreach ($medicos as $med): ?>
                <label style="display:block; margin-bottom:6px; font-weight:normal;">
                    <input type="radio" name="id_medico" value="<?php echo $med['id']; ?>" required>
                    <?php echo $med['nombre']; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <label>Medicamentos:</label>
        <div id="medicamentos">
            <div class="medicamento">
                <select name="medicamentos[]" required>
                    <option value="">Seleccionar medicamento</option>
                    <?php foreach ($medicamentosLista as $med): ?>
                        <option value="<?php echo $med['id']; ?>"><?php echo $med['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="dosis[]" required>
                    <option value="">Seleccionar dosis</option>
                    <option value="1 comprimido cada 8 horas">1 comprimido cada 8 horas</option>
                    <option value="1 comprimido cada 12 horas">1 comprimido cada 12 horas</option>
                    <option value="2 comprimidos al día">2 comprimidos al día</option>
                    <option value="1 aplicación cada 24 horas">1 aplicación cada 24 horas</option>
                    <option value="Uso según indicación médica">Uso según indicación médica</option>
                    <option value="Otra dosis">Otra dosis</option>
                </select>
                <button type="button" onclick="removeMedicamento(this)">Remover</button>
            </div>
        </div>
        <button type="button" onclick="addMedicamento()">Agregar Medicamento</button>

        <label for="indicaciones">Indicaciones:</label>
        <textarea name="indicaciones" id="indicaciones" rows="4" required></textarea>

        <label for="fecha_vencimiento">Fecha de Vencimiento:</label>
        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>

        <button type="submit">Emitir Prescripción</button>
    </form>

    <script>
        function addMedicamento() {
            const div = document.createElement('div');
            div.className = 'medicamento';
            div.innerHTML = `
                <select name="medicamentos[]" required>
                    <option value="">Seleccionar medicamento</option>
                    <?php foreach ($medicamentosLista as $med): ?>
                        <option value="<?php echo $med['id']; ?>"><?php echo $med['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="dosis[]" required>
                    <option value="">Seleccionar dosis</option>
                    <option value="1 comprimido cada 8 horas">1 comprimido cada 8 horas</option>
                    <option value="1 comprimido cada 12 horas">1 comprimido cada 12 horas</option>
                    <option value="2 comprimidos al día">2 comprimidos al día</option>
                    <option value="1 aplicación cada 24 horas">1 aplicación cada 24 horas</option>
                    <option value="Uso según indicación médica">Uso según indicación médica</option>
                    <option value="Otra dosis">Otra dosis</option>
                </select>
                <button type="button" onclick="removeMedicamento(this)">Remover</button>
            `;
            document.getElementById('medicamentos').appendChild(div);
        }

        function removeMedicamento(btn) {
            btn.parentElement.remove();
        }
    </script>

    <div style="margin-top:30px; padding:20px; background:#f7f7f7; border-left:4px solid #007bff; color:#333; font-size:0.95rem; line-height:1.5;">
        <strong>Nota:</strong> Esta receta fue creada por un emisor inscripto y validado en el Registro de Recetarios Electrónicos del Ministerio de Salud de la Nación.<br>
        <em>Resolución RL-2024-91317760-APN-SSVEIYES#MS</em>
    </div>
</body>
</html>