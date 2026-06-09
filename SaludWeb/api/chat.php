<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../db.php';
$config = [];
if (file_exists(__DIR__ . '/../config/openai.php')) {
    $config = require __DIR__ . '/../config/openai.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond_method_not_allowed('POST');
}

$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');
if ($message === '') {
    respond_error('El mensaje es obligatorio.', 400);
}

function fallback_answer($message) {
    $msg = mb_strtolower($message, 'UTF-8');
    if (strpos($msg, 'paciente') !== false) {
        return 'Puedes buscar pacientes desde el dashboard principal, editar sus datos y cargar triage o prescripciones para cada paciente.';
    }
    if (strpos($msg, 'receta') !== false || strpos($msg, 'farmacia') !== false) {
        return 'El módulo de recetas permite emitir prescripciones electrónicas y luego verlas en la lista de recetas. La farmacia puede dispensarlas desde el panel correspondiente.';
    }
    if (strpos($msg, 'api') !== false || strpos($msg, 'documentación') !== false) {
        return 'La API está disponible en el directorio /api. Puedes ver la documentación en la sección API Docs del dashboard.';
    }
    return 'Hola, soy el asistente de SaludWEB. Puedo ayudarte a entender las rutas del proyecto, cómo registrar pacientes, emitir recetas y administrar triages.';
}

$apiKey = $config['api_key'] ?? '';
$answer = null;

if (!empty($apiKey) && function_exists('curl_version')) {
    $payload = [
        'model' => $config['model'] ?? 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'Eres un asistente técnico que explica cómo usar SaludWEB.'],
            ['role' => 'user', 'content' => $message]
        ],
        'max_tokens' => 250,
        'temperature' => 0.7
    ];

    $ch = curl_init($config['base_url'] . '/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
        $decoded = json_decode($response, true);
        if (isset($decoded['choices'][0]['message']['content'])) {
            $answer = trim($decoded['choices'][0]['message']['content']);
        }
    }
}

if ($answer === null) {
    $answer = fallback_answer($message);
}

respond_ok(['answer' => $answer]);
