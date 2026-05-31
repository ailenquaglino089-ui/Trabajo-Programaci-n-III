<?php
// SaludWEB/db.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'pacientes';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");

    $pdo->exec("CREATE TABLE IF NOT EXISTS obras_sociales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre_obra VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS pacientes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dni VARCHAR(50) NULL,
        nombre VARCHAR(255) NOT NULL,
        id_obra_social INT NOT NULL DEFAULT 1,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_dni (dni),
        INDEX idx_nombre (nombre)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS triages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_paciente INT NOT NULL,
        nivel_gravedad TINYINT(3) NOT NULL,
        observaciones TEXT NULL,
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_paciente) REFERENCES pacientes(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS medicos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        matricula VARCHAR(100) NULL,
        especialidad VARCHAR(255) NULL,
        activo TINYINT(1) DEFAULT 1,
        creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS medicamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        descripcion TEXT NULL,
        creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS prescripciones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_paciente INT NOT NULL,
        id_medico INT NULL,
        medicamentos TEXT NOT NULL,
        indicaciones TEXT NULL,
        fecha_emision TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_vencimiento DATE NULL,
        estado VARCHAR(50) DEFAULT 'activa',
        qr_code VARCHAR(255) NULL,
        firma_digital VARCHAR(255) NULL,
        FOREIGN KEY (id_paciente) REFERENCES pacientes(id) ON DELETE CASCADE,
        FOREIGN KEY (id_medico) REFERENCES medicos(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $obrasCount = (int)$pdo->query('SELECT COUNT(*) FROM obras_sociales')->fetchColumn();
    if ($obrasCount === 0) {
        $pdo->exec("INSERT INTO obras_sociales (nombre_obra) VALUES ('Particular'), ('OSDE'), ('PAMI')");
    }

    $medicosCount = (int)$pdo->query('SELECT COUNT(*) FROM medicos')->fetchColumn();
    if ($medicosCount === 0) {
        $stmt = $pdo->prepare('INSERT INTO medicos (nombre, matricula, especialidad, activo) VALUES (?, ?, ?, 1)');
        $default = [
            ['Dr. Juan Pérez', '12345', 'Medicina General'],
            ['Dra. María López', '67890', 'Pediatría'],
            ['Dr. Sebastián Gómez', '11223', 'Cardiología'],
            ['Dra. Lucía Fernández', '44556', 'Dermatología'],
        ];
        foreach ($default as $m) {
            $stmt->execute($m);
        }
    }

    $medicamentosCount = (int)$pdo->query('SELECT COUNT(*) FROM medicamentos')->fetchColumn();
    if ($medicamentosCount === 0) {
        $stmt = $pdo->prepare('INSERT INTO medicamentos (nombre) VALUES (?)');
        foreach (['Paracetamol', 'Ibuprofeno', 'Amoxicilina', 'Loratadina', 'Omeprazol'] as $nombre) {
            $stmt->execute([$nombre]);
        }
    }

    $pacientesCount = (int)$pdo->query('SELECT COUNT(*) FROM pacientes')->fetchColumn();
    if ($pacientesCount === 0) {
        $stmt = $pdo->prepare('INSERT INTO pacientes (dni, nombre, id_obra_social, activo) VALUES (?, ?, ?, 1)');
        $defaults = [
            ['12345678', 'María García', 1],
            ['23456789', 'Carlos López', 2],
            ['34567890', 'Ana Fernández', 3],
        ];
        foreach ($defaults as $p) {
            $stmt->execute($p);
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Error de DB</title></head><body style="font-family:sans-serif; padding:30px; background:#f8f8f8;">';
    echo '<div style="max-width:820px; margin:auto; background:#fff; padding:24px; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.08);">';
    echo '<h1 style="color:#b91c1c;">Error al conectar con la base de datos</h1>';
    echo '<p style="font-size:1rem; color:#333;">Detalle técnico: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p>Revisa los parámetros en <code>db.php</code> y la configuración de MySQL.</p>';
    echo '</div></body></html>';
    exit;
}
?>
