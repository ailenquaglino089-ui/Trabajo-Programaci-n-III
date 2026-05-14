<?php
/**
 * Sistema de Ruteo Centralizado.
 * Mapea las URLs amigables a los archivos físicos en la carpeta SaludWEB.
 */
function cargarArchivo($nombreArchivo) {
    global $pdo;
    $ruta = dirname(__DIR__) . '/SaludWeb/' . $nombreArchivo;
    if (file_exists($ruta)) {
        include $ruta;
    } else {
        echo "Error: No se encuentra el archivo " . $nombreArchivo;
    }
}

function router() {
    global $pdo;
    // Limpieza de la URI para procesar la ruta solicitada
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base = '/prog3-clase2'; // Subdirectorio del proyecto

    if (strpos($uri, $base) === 0) {
        $uri = substr($uri, strlen($base));
    }

    $ruta = trim($uri, '/');

    // Delegación a la API si la ruta comienza con 'api'
    if ($ruta === 'api' || strpos($ruta, 'api/') === 0) {
        include __DIR__ . '/api.php';
        exit;
    }

    if ($ruta === '' || $ruta === 'index') {
        cargarArchivo('lista_pacientes.php');
        exit;
    }

    // Mapeo de rutas amigables a archivos específicos
    if ($ruta === 'lista') {
        cargarArchivo('lista_pacientes.php');
        exit;
    }

    if ($ruta === 'papelera') {
        cargarArchivo('papelera.php');
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

    if ($ruta === 'auditoria') {
        cargarArchivo('auditoria.php');
        exit;
    }

    if ($ruta === 'guardar_triage') {
        cargarArchivo('guardar_triage.php');
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

    if ($ruta === 'farmacia') {
        // Cargamos la interfaz de farmacia desde SaludWEB
        cargarArchivo('farmacia.php'); 
        exit;
    }

    if ($ruta === 'procesar_dispensa') {
        // Cargamos el procesador de entrega desde SaludWEB
        cargarArchivo('procesar_dispensa.php');
        exit;
    }

    if ($ruta === 'medicos') {
        cargarArchivo('lista_medicos.php');
        exit;
    }

    if ($ruta === 'api-docs' || $ruta === 'docs') {
        cargarArchivo('api_docs.php');
        exit;
    }

    // Fallback para rutas de escritorio o dashboard principal
    if ($ruta === 'desktop' || $ruta === 'escritorio') {
        cargarArchivo('escritorio.php');
        exit;
    }
}
?>