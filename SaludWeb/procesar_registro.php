<?php
// 1. Incluimos la conexión que creaste recién
require_once __DIR__ . '/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Capturamos los datos del formulario
    $dni = $_POST['dni'];
    $nombre = $_POST['nombre'];
    $obra = $_POST['id_obra_social'];

    try {
        // 3. Preparamos la consulta SQL
        $sql = "INSERT INTO pacientes (dni, nombre, id_obra_social) VALUES (:dni, :nombre, :obra)";
        $stmt = $pdo->prepare($sql);
        
        // 4. Ejecutamos pasando los valores
        $stmt->execute([
            ':dni' => $dni,
            ':nombre' => $nombre,
            ':obra' => $obra
        ]);

        // 5. Mensaje de éxito
        echo "<html><body style='font-family:sans-serif; text-align:center; padding-top:50px;'>";
        echo "<h1 style='color:green;'>✅ ¡Registro guardado en la DB!</h1>";
        echo "<p>El paciente <strong>$nombre</strong> fue dado de alta correctamente.</p>";
        echo "<br><a href='registro_paciente.php' style='text-decoration:none; color:blue;'>← Volver a registrar otro</a>";
        echo "</body></html>";
        
    } catch (PDOException $e) {
        // Si algo falla con la DB, nos avisa acá
        echo "<h1>❌ Error al guardar</h1>";
        echo "Detalle técnico: " . $e->getMessage();
    }
}
?>
