<?php
// SaludWEB/db.php
$host = 'localhost';
$db   = 'pacientes'; // Nombre que aparece en tu captura 446
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Aseguramos que la tabla de medicamentos exista para MRx Digital.
    $pdo->exec("CREATE TABLE IF NOT EXISTS medicamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        descripcion TEXT NULL,
        creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

    $countStmt = $pdo->query('SELECT COUNT(*) FROM medicamentos');
    $medicamentosCount = (int) $countStmt->fetchColumn();
    if ($medicamentosCount === 0) {
        $insertStmt = $pdo->prepare('INSERT INTO medicamentos (nombre) VALUES (?)');
        $defaultMedicamentos = ['Paracetamol', 'Ibuprofeno', 'Amoxicilina', 'Loratadina', 'Omeprazol'];
        foreach ($defaultMedicamentos as $nombre) {
            $insertStmt->execute([$nombre]);
        }
    }

    // Aseguramos que la tabla de médicos exista para MRx Digital.
    $pdo->exec("CREATE TABLE IF NOT EXISTS medicos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        matricula VARCHAR(100) NULL,
        especialidad VARCHAR(255) NULL,
        activo TINYINT(1) DEFAULT 1,
        creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

    $countMedicosStmt = $pdo->query('SELECT COUNT(*) FROM medicos');
    $medicosCount = (int) $countMedicosStmt->fetchColumn();
    if ($medicosCount === 0) {
        $insertMedicoStmt = $pdo->prepare('INSERT INTO medicos (nombre, matricula, especialidad, activo) VALUES (?, ?, ?, 1)');
        $defaultMedicos = [
            ['Dr. Juan Pérez', '12345', 'Medicina General'],
            ['Dra. María López', '67890', 'Pediatría'],
            ['Dr. Sebastián Gómez', '11223', 'Cardiología'],
            ['Dra. Lucía Fernández', '44556', 'Dermatología'],
        ];
        foreach ($defaultMedicos as $medico) {
            $insertMedicoStmt->execute($medico);
        }
    }
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
