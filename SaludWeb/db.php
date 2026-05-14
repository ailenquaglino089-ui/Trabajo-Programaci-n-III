<?php
// SaludWEB/db.php

// Parámetros de conexión a la base de datos MySQL
$host = 'localhost';
$db   = 'pacientes'; // Nombre que aparece en tu captura 446
$user = 'root';
$pass = '';

try {
    // Establecer conexión usando PDO con soporte para caracteres UTF-8
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Migración automática: Aseguramos que la tabla de medicamentos exista
    $pdo->exec("CREATE TABLE IF NOT EXISTS medicamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        descripcion TEXT NULL,
        creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

    // Si la tabla de medicamentos está vacía, insertamos valores por defecto para pruebas
    $countStmt = $pdo->query('SELECT COUNT(*) FROM medicamentos');
    $medicamentosCount = (int) $countStmt->fetchColumn();
    if ($medicamentosCount === 0) {
        $insertStmt = $pdo->prepare('INSERT INTO medicamentos (nombre) VALUES (?)');
        $defaultMedicamentos = ['Paracetamol', 'Ibuprofeno', 'Amoxicilina', 'Loratadina', 'Omeprazol'];
        foreach ($defaultMedicamentos as $nombre) {
            $insertStmt->execute([$nombre]);
        }
    }

    // Migración automática: Aseguramos que la tabla de médicos exista
    $pdo->exec("CREATE TABLE IF NOT EXISTS medicos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        matricula VARCHAR(100) NULL,
        especialidad VARCHAR(255) NULL,
        activo TINYINT(1) DEFAULT 1,
        creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

    // Poblado inicial de médicos si la tabla está recién creada
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

    // Migración automática: Aseguramos que la tabla de prescripciones exista
    $pdo->exec("CREATE TABLE IF NOT EXISTS prescripciones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_paciente INT NOT NULL,
        id_medico INT NOT NULL,
        medicamentos JSON NOT NULL,
        indicaciones TEXT NULL,
        fecha_emision TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_vencimiento DATE NULL,
        estado VARCHAR(50) DEFAULT 'activa',
        qr_code VARCHAR(255) NULL,
        firma_digital VARCHAR(255) NULL,
        FOREIGN KEY (id_paciente) REFERENCES pacientes(id),
        FOREIGN KEY (id_medico) REFERENCES medicos(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
} catch (PDOException $e) {
    // En lugar de die(), lanzamos la excepción para que el llamador decida cómo mostrarla
    throw $e;
}
?>
