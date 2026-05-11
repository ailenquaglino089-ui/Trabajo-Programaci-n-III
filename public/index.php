<?php
// public/index.php

// Habilitación de errores para depuración durante el desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cargamos la conexión una sola vez al principio
require_once dirname(__DIR__) . '/SaludWEB/db.php';

// Cargamos el router separado en otra pestaña/archivo
require_once __DIR__ . '/router.php';

router();
?>