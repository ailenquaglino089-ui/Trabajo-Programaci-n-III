<?php
require_once __DIR__ . '/db.php'; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibimos los datos del formulario de triage
    $id_paciente = $_POST['id_paciente'];
    $fiebre = $_POST['fiebre'];
    $tos = $_POST['tos'];
    $gravedad = $_POST['nivel_gravedad'];

    try {
        // Insertamos en la tabla 'triages' que creaste en SQL
        $sql = "INSERT INTO triages (id_paciente, fiebre, tos, nivel_gravedad) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_paciente, $fiebre, $tos, $gravedad]);

        echo "<html><body style='font-family:sans-serif; text-align:center; padding-top:50px;'>";
        echo "<h1 style='color:green;'>✅ Triage Guardado</h1>";
        echo "<p>Los datos médicos se asociaron correctamente al paciente.</p>";
        echo "<br><a href='lista_pacientes.php' style='text-decoration:none; color:blue;'>← Volver a la lista</a>";
        echo "</body></html>";

    } catch (PDOException $e) {
        echo "<h1>❌ Error al guardar el triage</h1>";
        echo "Detalle: " . $e->getMessage();
    }
}
?>
