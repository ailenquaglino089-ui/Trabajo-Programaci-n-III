<?php
function cargarArchivo($nombreArchivo) {
    global $pdo;
    $ruta = dirname(__DIR__) . '/SaludWEB/' . $nombreArchivo;
    if (file_exists($ruta)) {
        include $ruta;
    } else {
        echo "Error: No se encuentra el archivo " . $nombreArchivo;
    }
}

function router() {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base = '/prog3-clase2';

    if (strpos($uri, $base) === 0) {
        $uri = substr($uri, strlen($base));
    }

    $ruta = trim($uri, '/');

    if ($ruta === 'api' || strpos($ruta, 'api/') === 0) {
        include __DIR__ . '/api.php';
        exit;
    }

    if ($ruta === '' || $ruta === 'index') {
        cargarArchivo('lista_pacientes.php');
        exit;
    }

    if ($ruta === 'lista') {
        cargarArchivo('lista_pacientes.php');
        exit;
    }

    if ($ruta === 'papelera') {
        cargarArchivo('papelera.php');
        exit;
    }

    if ($ruta === 'restaurar') {
        cargarArchivo('restaurar_paciente.php');
        exit;
    }

    if ($ruta === 'registro' || $ruta === 'formulario') {
        cargarArchivo('registro_paciente.php');
        exit;
    }

    if ($ruta === 'editar') {
        cargarArchivo('editar_paciente.php');
        exit;
    }

    if ($ruta === 'triage') {
        cargarArchivo('triage.php');
        exit;
    }

    if ($ruta === 'ver_triage') {
        cargarArchivo('ver_triage.php');
        exit;
    }

    if ($ruta === 'eliminar') {
        cargarArchivo('eliminar_paciente.php');
        exit;
    }

    if ($ruta === 'auditoria') {
        cargarArchivo('auditoria.php');
        exit;
    }

    if ($ruta === 'guardar_triage') {
        cargarArchivo('guardar_triage.php');
        exit;
    }

    if ($ruta === 'chat') {
        cargarArchivo('chat.php');
        exit;
    }

    if ($ruta === 'emitir_prescripcion') {
        cargarArchivo('emitir_prescripcion.php');
        exit;
    }

    if ($ruta === 'lista_prescripciones') {
        cargarArchivo('lista_prescripciones.php');
        exit;
    }

    if ($ruta === 'api-docs' || $ruta === 'docs') {
        cargarArchivo('api_docs.php');
        exit;
    }

    if ($ruta === 'desktop' || $ruta === 'escritorio') {
        cargarArchivo('escritorio.php');
        exit;
    }
}
?>