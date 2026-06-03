<?php
// SaludWeb/lista_medicos.php
require_once __DIR__ . '/db.php';
$medicos = $pdo->query("SELECT * FROM medicos ORDER BY activo DESC, nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Médicos - SaludWEB</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8f9fc; padding: 30px; }
        .card { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); max-width: 1000px; margin: auto; }
        h1 { color: #1e293b; margin-top: 0; display: flex; align-items: center; gap: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; color: #64748b; font-size: 0.75rem; padding: 12px; border-bottom: 2px solid #f1f5f9; text-transform: uppercase; }
        td { padding: 14px 12px; border-bottom: 1px solid #f1f5f9; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .active { background: #d1fae5; color: #065f46; }
        .inactive { background: #fee2e2; color: #991b1b; }
        .btn-toggle { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.8rem; }
        .btn-back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #6366f1; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <a href="lista" class="btn-back">← Volver al Dashboard</a>
        <h1>👨‍⚕️ Panel de Profesionales</h1>
        <p>Administra los médicos habilitados para emitir recetas electrónicas.</p>

        <table>
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Nombre del Profesional</th>
                    <th>Matrícula</th>
                    <th>Especialidad</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medicos as $m): ?>
                <tr id="medico-<?php echo $m['id']; ?>">
                    <td>
                        <span class="badge <?php echo $m['activo'] ? 'active' : 'inactive'; ?>">
                            <?php echo $m['activo'] ? 'OPERATIVO' : 'INACTIVO'; ?>
                        </span>
                    </td>
                    <td><strong><?php echo htmlspecialchars($m['nombre']); ?></strong></td>
                    <td><code><?php echo htmlspecialchars($m['matricula'] ?: 'N/A'); ?></code></td>
                    <td><?php echo htmlspecialchars($m['especialidad'] ?: 'General'); ?></td>
                    <td>
                        <button class="btn-toggle" 
                                style="background: <?php echo $m['activo'] ? '#fee2e2' : '#d1fae5'; ?>; color: <?php echo $m['activo'] ? '#991b1b' : '#065f46'; ?>"
                                onclick="toggleMedico(<?php echo $m['id']; ?>, <?php echo $m['activo'] ? 0 : 1; ?>)">
                            <?php echo $m['activo'] ? 'Desactivar' : 'Activar'; ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    async function toggleMedico(id, nuevoEstado) {
        const apiUrl = 'api/medicos?id=' + id;
        
        try {
            const response = await fetch(apiUrl, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ activo: nuevoEstado })
            });

            if (response.ok) {
                location.reload(); // Recarga para actualizar visualmente
            } else {
                alert('Error al actualizar el estado del médico.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión con la API.');
        }
    }
    </script>
</body>
</html>