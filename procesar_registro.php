<?php
// 1. Incluimos la conexión que creaste recién
require_once __DIR__ . '/SaludWeb/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Capturamos los datos del formulario
    $dni = $_POST['dni'];
    $nombre = $_POST['nombre'];
    $obra = $_POST['id_obra_social'];

    try {
        // 3. Preparamos la consulta SQL
        // Usamos ON DUPLICATE KEY UPDATE para evitar el error de DNI duplicado y actualizar el registro existente
        $sql = "INSERT INTO pacientes (dni, nombre, id_obra_social, activo) VALUES (:dni, :nombre, :obra, 1) 
                ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), id_obra_social = VALUES(id_obra_social), activo = 1";
        $stmt = $pdo->prepare($sql);
        
        // 4. Ejecutamos pasando los valores
        $stmt->execute([
            ':dni' => $dni,
            ':nombre' => $nombre,
            ':obra' => $obra
        ]);

        // Verificamos si fue una inserción o una actualización de un DNI existente
        $esActualizacion = $stmt->rowCount() == 2;
        $titulo = $esActualizacion ? "✅ ¡Datos Actualizados!" : "✅ ¡Registro guardado!";
        $texto = $esActualizacion ? "Los datos para el DNI <strong>$dni</strong> han sido actualizados." : "El paciente <strong>$nombre</strong> fue dado de alta correctamente.";

        // 5. Mensaje de éxito
        echo "<html><body style='font-family:sans-serif; text-align:center; padding-top:50px;'>";
        echo "<h1 style='color:green;'>$titulo</h1>";
        echo "<p>$texto</p>";
        echo "<br><a href='lista' style='text-decoration:none; color:blue;'>← Volver al Dashboard</a>";
        echo "</body></html>";
        
    } catch (PDOException $e) {
        // Si algo falla con la DB, nos avisa acá
        echo "<h1>❌ Error al guardar</h1>";
        echo "Detalle técnico: " . $e->getMessage();
    }
}
?>