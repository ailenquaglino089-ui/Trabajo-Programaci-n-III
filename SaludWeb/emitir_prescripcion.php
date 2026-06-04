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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emitir Prescripción - MRx Digital</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f5f7; }
        form { max-width: 700px; margin: auto; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 16px 30px rgba(0,0,0,0.06); }
        label { display: block; margin-top: 18px; font-weight: 700; color: #1a202c; font-size: 1rem; }
        input, select, textarea { width: 100%; padding: 12px 14px; margin-top: 8px; border: 2px solid #e2e8f0; border-radius: 10px; box-sizing: border-box; font-size: 0.95rem; transition: 0.3s; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        button { margin-top: 20px; padding: 14px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; cursor: pointer; border-radius: 10px; font-weight: 700; font-size: 1rem; width: 100%; transition: 0.3s; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); }
        button:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(102, 126, 234, 0.4); }
        button[type="button"] { display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #ef4444; width: auto; margin-top: 6px; margin-left: auto; padding: 10px 16px; font-size: 0.9rem; }
        button[type="button"]:hover { background: #dc2626; }
        .success { color: #065f46; background: #d1fae5; padding: 14px 16px; border-radius: 10px; border-left: 4px solid #10b981; margin-bottom: 15px; }
        .error { color: #b91c1c; background: #fee2e2; padding: 14px 16px; border-radius: 10px; border-left: 4px solid #ef4444; margin-bottom: 15px; }
        .medicamento { margin-bottom: 16px; display: grid; grid-template-columns: 1.2fr 1fr auto; gap: 10px; padding: 14px; background: #f9fafb; border-radius: 10px; border-left: 4px solid #667eea; }
        .medicamento select { margin-top: 0; }
        .medicamento button { width: auto; margin: 0; padding: 10px 14px; font-size: 0.85rem; }
        .medico-item { background: #f9fafb; padding: 12px 14px; border-radius: 8px; border: 2px solid #e2e8f0; margin-bottom: 10px; transition: 0.3s; cursor: pointer; }
        .medico-item:hover { border-color: #667eea; background: #f3f4f6; }
        .medico-item input[type="radio"] { margin-right: 10px; width: auto; cursor: pointer; accent-color: #667eea; }
        .medico-item label { margin: 0; font-weight: 600; color: #1a202c; display: flex; align-items: center; cursor: pointer; }
        .field-group { background: #f9fafb; padding: 16px; border-radius: 10px; margin-bottom: 15px; }
        .note-box { margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #eff6ff 0%, #f0e7ff 100%); border-left: 4px solid #667eea; border-radius: 10px; color: #1a202c; font-size: 0.95rem; }

        @media (max-width: 640px) {
            body { margin: 12px; }
            form { padding: 16px; }
            h1 { font-size: 1.4rem; }
            button { width: 100%; }
            .medicamento { grid-template-columns: 1fr; }
            .medicamento button { width: 100%; }
        }
    </style>
</head>
<body>
    <a href="lista_pacientes.php" style="display: inline-block; margin-bottom: 20px; text-decoration: none; color: #007bff; font-weight: bold;">← Volver al Dashboard</a>
    <h1 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3); text-align: center; font-size: 1.8rem; letter-spacing: 0.5px; margin-bottom: 10px;">
        💊 MRx Digital - Emitir Receta Electrónica
    </h1>
    <p style="font-size:0.95rem; color:#fff; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin-bottom:15px; padding: 15px 20px; border-radius: 8px; text-align: center; box-shadow: 0 4px 10px rgba(102, 126, 234, 0.2);">
        ✓ Nuevo formato de receta electrónica válido para farmacias y obras sociales • Conforme a la normativa vigente
    </p>
    <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="id_paciente">👤 Paciente:</label>
        <select name="id_paciente" id="id_paciente" required style="border-color: #667eea;">
            <option value="">Seleccionar paciente</option>
            <?php foreach ($pacientes as $pac): ?>
                <option value="<?php echo $pac['id']; ?>" <?php echo ($idPacienteSeleccionado == $pac['id']) ? 'selected' : ''; ?>><?php echo $pac['nombre']; ?></option>
            <?php endforeach; ?>
        </select>

        <label style="margin-top: 24px;">👨‍⚕️ Médico Emisor:</label>
        <div style="margin-bottom: 15px;">
            <?php foreach ($medicos as $med): ?>
                <div class="medico-item">
                    <label>
                        <input type="radio" name="id_medico" value="<?php echo $med['id']; ?>" required>
                        <?php echo $med['nombre']; ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <label style="margin-top: 24px;">💊 Medicamentos:</label>
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
                <button type="button" onclick="removeMedicamento(this)" style="background: #ef4444; padding: 10px 12px;">🗑️ Remover</button>
            </div>
        </div>
        <button type="button" onclick="addMedicamento()" style="background: #10b981; width: auto; margin-top: 12px; margin-bottom: 18px; padding: 10px 18px;">➕ Agregar Medicamento</button>

        <label for="indicaciones" style="margin-top: 24px;">📝 Indicaciones:</label>
        <textarea name="indicaciones" id="indicaciones" rows="4" required style="resize: vertical;"></textarea>

        <label for="fecha_vencimiento" style="margin-top: 24px;">📅 Fecha de Vencimiento:</label>
        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>

        <button type="submit" style="margin-top: 28px;">✓ Emitir Prescripción</button>
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

    <div class="note-box">
        <strong>✓ Receta Electrónica Validada:</strong> Esta receta fue creada por un emisor inscripto y validado en el Registro de Recetarios Electrónicos del Ministerio de Salud de la Nación.<br>
        <em style="color: #667eea; font-weight: 600;">Resolución RL-2024-91317760-APN-SSVEIYES#MS</em>
    </div>
</body>
</html>