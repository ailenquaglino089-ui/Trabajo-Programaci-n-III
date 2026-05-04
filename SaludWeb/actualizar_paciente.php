<?php
require_once __DIR__ . '/db.php';;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $dni = $_POST['dni'];
    $nombre = $_POST['nombre'];
    $obra = $_POST['id_obra_social'];

    try {
        $sql = "UPDATE pacientes SET dni = ?, nombre = ?, id_obra_social = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$dni, $nombre, $obra, $id]);

        header("Location: lista?mensaje=actualizado");
        exit();
    } catch (PDOException $e) {
        echo "Error al actualizar: " . $e->getMessage();
    }
}
?>
