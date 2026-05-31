<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SaludWEB - Registro</title>
</head>
<body>
    <h1>SaludWEB: Registro de Paciente Nuevo</h1>
    
    <form action="procesar_registro.php" method="POST">
        <p>
            <label>DNI del Paciente:</label><br>
            <input type="number" name="dni" required>
        </p>

        <p>
            <label>Nombre Completo:</label><br>
            <input type="text" name="nombre" required>
        </p>

        <p>
            <label>Obra Social:</label><br>
            <select name="id_obra_social">
                <option value="1">Particular</option>
                <option value="2">OSDE</option>
                <option value="3">PAMI</option>
            </select>
        </p>

        <button type="submit">Guardar y Continuar al Triage</button>
    </form>
</body>
</html>