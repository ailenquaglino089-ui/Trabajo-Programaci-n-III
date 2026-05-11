<?php
// public/api.php

// Configuración de cabeceras para una API REST JSON y reporte de errores para desarrollo
header('Content-Type: application/json; charset=utf-8');
// Desactivamos la visualización de errores HTML para no romper el JSON
ini_set('display_errors', 0);
ini_set('html_errors', 0);
error_reporting(E_ALL);

// Asegurar la conexión a la base de datos utilizando el objeto $pdo global
global $pdo;
if (!isset($pdo)) {
    require_once dirname(__DIR__) . '/SaludWeb/db.php';
}

/**
 * Envía una respuesta JSON estandarizada y finaliza la ejecución.
 */
function apiResponse($data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Envía un mensaje de error en formato JSON.
 */
function apiError(string $message, int $status = 400): void {
    apiResponse(['error' => $message, 'status' => $status], $status);
}

/**
 * Construye la URL base del proyecto para la documentación de la API.
 */
function getBaseUrl(): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $projectPath = dirname(dirname($_SERVER['SCRIPT_NAME']));
    return rtrim($scheme . '://' . $host . $projectPath, '/');
}

/**
 * Define la especificación OpenAPI (Swagger) para la documentación de la API.
 */
function getApiDocs(): array {
    $serverUrl = getBaseUrl();
    return [
        'openapi' => '3.0.0',
        'info' => [
            'title' => 'SaludWEB API',
            'description' => 'Documentación automática de la API REST para gestión de pacientes y asistente inteligente.',
            'version' => '1.0.0'
        ],
        'servers' => [
            ['url' => $serverUrl]
        ],
        'paths' => [
            '/api/pacientes' => [
                'get' => [
                    'summary' => 'Listar pacientes',
                    'description' => 'Devuelve lista de pacientes activos.',
                    'parameters' => [
                        ['name' => 'activo', 'in' => 'query', 'schema' => ['type' => 'integer'], 'description' => '0 para pacientes inactivos, 1 para activos', 'required' => false]
                    ],
                    'responses' => ['200' => ['description' => 'Lista de pacientes', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PacientesResponse']]]]]
                ],
                'post' => [
                    'summary' => 'Crear paciente',
                    'description' => 'Crea un nuevo paciente en la base de datos.',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PacienteInput']]]],
                    'responses' => ['201' => ['description' => 'Paciente creado', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateResponse']]]]]
                ]
            ],
            '/api/pacientes/{id}' => [
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'get' => [
                    'summary' => 'Ver paciente',
                    'description' => 'Devuelve información de un paciente específico.',
                    'responses' => ['200' => ['description' => 'Paciente encontrado', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PacienteResponse']]]], '404' => ['description' => 'Paciente no encontrado']]
                ],
                'put' => [
                    'summary' => 'Actualizar paciente',
                    'description' => 'Actualiza los datos de un paciente existente.',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PacienteInput']]]],
                    'responses' => ['200' => ['description' => 'Paciente actualizado', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MessageResponse']]]], '404' => ['description' => 'Paciente no encontrado']]
                ],
                'patch' => [
                    'summary' => 'Actualizar paciente (parcial)',
                    'description' => 'Actualiza los datos de un paciente existente.',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PacienteInput']]]],
                    'responses' => ['200' => ['description' => 'Paciente actualizado', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MessageResponse']]]], '404' => ['description' => 'Paciente no encontrado']]
                ],
                'delete' => [
                    'summary' => 'Eliminar paciente',
                    'description' => 'Realiza un soft delete marcando al paciente como inactivo.',
                    'responses' => ['200' => ['description' => 'Paciente eliminado', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MessageResponse']]]], '404' => ['description' => 'Paciente no encontrado']]
                ]
            ],
            '/api/pacientes/{id}/restore' => [
                'patch' => [
                    'summary' => 'Restaurar paciente',
                    'description' => 'Restaurar un paciente marcado como inactivo.',
                    'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                    'responses' => ['200' => ['description' => 'Paciente restaurado', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MessageResponse']]]], '404' => ['description' => 'Paciente no encontrado']]
                ]
            ],
            '/api/triage' => [
                'post' => [
                    'summary' => 'Registrar Triage',
                    'description' => 'Registra un nuevo triage para un paciente existente o crea uno nuevo si no existe.',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/TriageInput']]]],
                    'responses' => ['201' => ['description' => 'Triage registrado', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateResponse']]]]]
                ]
            ],
            '/api/chat' => [
                'post' => [
                    'summary' => 'Enviar mensaje al asistente AI',
                    'description' => 'Envía una pregunta para que el asistente responda usando un modelo local o OpenAI.',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => ['message' => ['type' => 'string']], 'required' => ['message']]]]],
                    'responses' => ['200' => ['description' => 'Respuesta del asistente', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ChatResponse']]]]]
                ]
            ],
            '/api/prescripciones' => [
                'get' => [
                    'summary' => 'Listar prescripciones',
                    'description' => 'Devuelve lista de prescripciones activas.',
                    'parameters' => [
                        ['name' => 'estado', 'in' => 'query', 'schema' => ['type' => 'string'], 'description' => 'Filtrar por estado: activa, dispensada, etc.', 'required' => false]
                    ],
                    'responses' => ['200' => ['description' => 'Lista de prescripciones', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PrescripcionesResponse']]]]]
                ],
                'post' => [
                    'summary' => 'Crear prescripción',
                    'description' => 'Crea una nueva prescripción electrónica.',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PrescripcionInput']]]],
                    'responses' => ['201' => ['description' => 'Prescripción creada', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateResponse']]]]]
                ]
            ],
            '/api/prescripciones/{id}' => [
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'get' => [
                    'summary' => 'Ver prescripción',
                    'description' => 'Devuelve información de una prescripción específica.',
                    'responses' => ['200' => ['description' => 'Prescripción encontrada', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PrescripcionResponse']]]], '404' => ['description' => 'Prescripción no encontrada']]
                ],
                'put' => [
                    'summary' => 'Actualizar prescripción',
                    'description' => 'Actualiza el estado de una prescripción.',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PrescripcionUpdate']]]],
                    'responses' => ['200' => ['description' => 'Prescripción actualizada', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MessageResponse']]]], '404' => ['description' => 'Prescripción no encontrada']]
                ]
            ],
            '/api/medicos' => [
                'get' => [
                    'summary' => 'Listar médicos',
                    'description' => 'Devuelve lista de médicos activos.',
                    'responses' => ['200' => ['description' => 'Lista de médicos', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MedicosResponse']]]]]
                ],
                'post' => [
                    'summary' => 'Crear médico',
                    'description' => 'Registra un nuevo médico emisor.',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MedicoInput']]]],
                    'responses' => ['201' => ['description' => 'Médico creado', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateResponse']]]]]
                ]
            ],
            '/api/docs' => [
                'get' => [
                    'summary' => 'Documentación automática',
                    'description' => 'Devuelve la especificación OpenAPI de la API.',
                    'responses' => ['200' => ['description' => 'Especificación OpenAPI', 'content' => ['application/json' => ['schema' => ['type' => 'object']]]]]
                ]
            ]
        ],
        'components' => [
            'schemas' => [
                'PacienteInput' => [
                    'type' => 'object',
                    'properties' => ['dni' => ['type' => 'string'], 'nombre' => ['type' => 'string'], 'id_obra_social' => ['type' => 'integer']],
                    'required' => ['dni', 'nombre']
                ],
                'Paciente' => [
                    'type' => 'object',
                    'properties' => ['id' => ['type' => 'integer'], 'dni' => ['type' => 'string'], 'nombre' => ['type' => 'string'], 'id_obra_social' => ['type' => 'integer'], 'activo' => ['type' => 'integer'], 'nombre_obra' => ['type' => 'string'], 'nivel_gravedad' => ['type' => 'integer'], 'ultimo_triage_fecha' => ['type' => 'string', 'format' => 'date-time']]
                ],
                'PacientesResponse' => ['type' => 'object', 'properties' => ['data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Paciente']]]],
                'PacienteResponse' => ['type' => 'object', 'properties' => ['data' => ['$ref' => '#/components/schemas/Paciente']]],
                'CreateResponse' => ['type' => 'object', 'properties' => ['message' => ['type' => 'string'], 'id' => ['type' => 'integer']]],
                'MessageResponse' => ['type' => 'object', 'properties' => ['message' => ['type' => 'string']]],
                'ChatResponse' => ['type' => 'object', 'properties' => ['data' => ['type' => 'object', 'properties' => ['answer' => ['type' => 'string']]]]],
                'PrescripcionInput' => [
                    'type' => 'object',
                    'properties' => ['id_paciente' => ['type' => 'integer'], 'id_medico' => ['type' => 'integer'], 'medicamentos' => ['type' => 'array', 'items' => ['type' => 'object']], 'indicaciones' => ['type' => 'string'], 'fecha_vencimiento' => ['type' => 'string', 'format' => 'date']],
                    'required' => ['id_paciente', 'id_medico', 'medicamentos']
                ],
                'Prescripcion' => [
                    'type' => 'object',
                    'properties' => ['id' => ['type' => 'integer'], 'id_paciente' => ['type' => 'integer'], 'id_medico' => ['type' => 'integer'], 'fecha_emision' => ['type' => 'string', 'format' => 'date-time'], 'fecha_vencimiento' => ['type' => 'string', 'format' => 'date'], 'medicamentos' => ['type' => 'string'], 'indicaciones' => ['type' => 'string'], 'estado' => ['type' => 'string'], 'qr_code' => ['type' => 'string'], 'firma_digital' => ['type' => 'string']]
                ],
                'PrescripcionesResponse' => ['type' => 'object', 'properties' => ['data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Prescripcion']]]],
                'PrescripcionResponse' => ['type' => 'object', 'properties' => ['data' => ['$ref' => '#/components/schemas/Prescripcion']]],
                'PrescripcionUpdate' => [
                    'type' => 'object',
                    'properties' => ['estado' => ['type' => 'string']],
                    'required' => ['estado']
                ],
                'MedicoInput' => [
                    'type' => 'object',
                    'properties' => ['nombre' => ['type' => 'string'], 'matricula' => ['type' => 'string'], 'especialidad' => ['type' => 'string']],
                    'required' => ['nombre', 'matricula']
                ],
                'Medico' => [
                    'type' => 'object',
                    'properties' => ['id' => ['type' => 'integer'], 'nombre' => ['type' => 'string'], 'matricula' => ['type' => 'string'], 'especialidad' => ['type' => 'string'], 'activo' => ['type' => 'integer']]
                ],
                'MedicosResponse' => ['type' => 'object', 'properties' => ['data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Medico']]]]
            ]
        ]
    ];
}

/**
 * Endpoint para mostrar la documentación técnica.
 */
function apiDocs(): void {
    apiResponse(getApiDocs());
}

/**
 * Captura el input JSON del cuerpo de la petición (request body).
 */
function getJsonInput(): array {
    $input = file_get_contents('php://input');
    if (!$input) {
        return $_POST;
    }

    $data = json_decode($input, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        return is_array($data) ? $data : [];
    }

    parse_str($input, $parsed);
    return $parsed;
}

/**
 * Intenta obtener la API KEY de OpenAI desde diversas fuentes de configuración.
 */
function getOpenAIKey(): ?string {
    $key = getenv('OPENAI_API_KEY');
    if (!$key) {
        $key = $_SERVER['OPENAI_API_KEY'] ?? $_ENV['OPENAI_API_KEY'] ?? null;
    }
    if ($key) {
        return trim($key);
    }

    $configPath = dirname(__DIR__) . '/config/openai.php';
    if (file_exists($configPath)) {
        $config = include $configPath;
        if (is_array($config) && !empty($config['OPENAI_API_KEY'])) {
            return trim($config['OPENAI_API_KEY']);
        }
    }

    return null;
}

/**
 * Realiza una petición a la API de OpenAI para el chat asistente.
 */
function callOpenAI(string $message): string {
    $apiKey = getOpenAIKey();
    if (!$apiKey || !function_exists('curl_init')) {
        return localChatAnswer($message, !$apiKey ? '' : 'cURL no está disponible en este servidor.');
    }

    $payload = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'Eres un asistente técnico para un proyecto PHP de gestión de pacientes. Responde en español.'],
            ['role' => 'user', 'content' => $message],
        ],
        'temperature' => 0.7,
        'max_tokens' => 800,
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $response = curl_exec($ch);
    $curlError = function_exists('curl_error') ? curl_error($ch) : '';
    curl_close($ch);

    if (!$response || $curlError) {
        return localChatAnswer($message, 'Error al conectar con OpenAI: ' . $curlError);
    }

    $decoded = json_decode($response, true);
    if (!isset($decoded['choices'][0]['message']['content'])) {
        return localChatAnswer($message, 'Respuesta inválida de OpenAI.');
    }

    return trim($decoded['choices'][0]['message']['content']);
}

/**
 * Sistema de respuestas local basado en palabras clave (Fallback si no hay OpenAI).
 */
function localChatAnswer(string $message, string $error = ''): string {
    $lower = mb_strtolower($message, 'UTF-8');

    if ($error !== '') {
        $intro = "No puedo usar OpenAI en este servidor. \n$error\n\n";
    } else {
        $intro = '';
    }

    if (stripos($lower, 'ruta') !== false || stripos($lower, 'endpoint') !== false || stripos($lower, 'api') !== false) {
        return $intro . "Rutas disponibles:\n- GET /prog3-clase2/api/pacientes\n- GET /prog3-clase2/api/pacientes/{id}\n- POST /prog3-clase2/api/pacientes\n- PUT /prog3-clase2/api/pacientes/{id}\n- PATCH /prog3-clase2/api/pacientes/{id}/restore\n- DELETE /prog3-clase2/api/pacientes/{id}\n- POST /prog3-clase2/api/triage\n- GET /prog3-clase2/api/prescripciones\n- GET /prog3-clase2/api/prescripciones/{id}\n- POST /prog3-clase2/api/prescripciones\n- PUT /prog3-clase2/api/prescripciones/{id}\n- GET /prog3-clase2/api/medicos\n- POST /prog3-clase2/api/medicos\n- POST /prog3-clase2/api/chat\n- GET /prog3-clase2/api/docs";
    }

    if (stripos($lower, 'paciente') !== false || stripos($lower, 'dni') !== false || stripos($lower, 'nombre') !== false) {
        return $intro . "Los pacientes se almacenan en la tabla 'pacientes' con campos principales: id, dni, nombre, id_obra_social, activo. Puedes crear y actualizar registros usando las rutas REST. También hay triages en la tabla 'triages' para historial clínico.";
    }

    if (stripos($lower, 'chat') !== false || stripos($lower, 'ayuda') !== false || stripos($lower, 'inteligencia') !== false) {
        return $intro . "Este asistente puede responder preguntas sobre el proyecto y las rutas API. Si quieres conectar a OpenAI, define la variable de entorno OPENAI_API_KEY en el servidor. Mientras tanto, uso respuestas locales basadas en el proyecto.";
    }

    return $intro . "Estoy listo para ayudarte con el proyecto. Puedes preguntar sobre las rutas API, cómo crear o actualizar pacientes, o cómo usar esta aplicación. Si deseas respuestas avanzadas, configura OPENAI_API_KEY en el servidor.";
}

/**
 * Controlador para la funcionalidad de chat de asistencia.
 */
function chatAssistant(PDO $pdo): void {
    $data = getJsonInput();
    $message = trim($data['message'] ?? $data['question'] ?? '');
    if ($message === '') {
        apiError('Mensaje de chat requerido', 422);
    }

    $answer = callOpenAI($message);
    apiResponse(['data' => ['answer' => $answer]]);
}

/**
 * Obtiene el filtro de estado (activo/inactivo) de los parámetros GET.
 */
function getActiveFilter(): int {
    if (isset($_GET['activo']) && $_GET['activo'] === '0') {
        return 0;
    }
    return 1;
}

/**
 * Lista pacientes incluyendo su última clasificación de triage y obra social.
 */
function listPacientes(PDO $pdo): void {
    $activo = getActiveFilter();

    $sql = "SELECT p.*, o.nombre_obra, t.nivel_gravedad, t.fecha AS ultimo_triage_fecha
            FROM pacientes p
            LEFT JOIN obras_sociales o ON p.id_obra_social = o.id
            LEFT JOIN (
                SELECT tr.id_paciente, tr.nivel_gravedad, tr.fecha
                FROM triages tr
                WHERE tr.id IN (SELECT MAX(id) FROM triages GROUP BY id_paciente)
            ) t ON p.id = t.id_paciente
            WHERE p.activo = ?
            ORDER BY p.nombre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$activo]);
    apiResponse(['data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

/**
 * Obtiene el detalle de un paciente y su historial de triages.
 */
function getPaciente(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare(
        "SELECT p.*, o.nombre_obra
         FROM pacientes p
         LEFT JOIN obras_sociales o ON p.id_obra_social = o.id
         WHERE p.id = ?"
    );
    $stmt->execute([$id]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        apiError('Paciente no encontrado', 404);
    }

    $triageStmt = $pdo->prepare(
        "SELECT id, nivel_gravedad, observaciones, fecha
         FROM triages
         WHERE id_paciente = ?
         ORDER BY fecha DESC"
    );
    $triageStmt->execute([$id]);
    $paciente['triage_historial'] = $triageStmt->fetchAll(PDO::FETCH_ASSOC);

    apiResponse(['data' => $paciente]);
}

/**
 * Registra un nuevo paciente.
 */
function createPaciente(PDO $pdo): void {
    $data = getJsonInput();
    $dni = trim($data['dni'] ?? '');
    $nombre = trim($data['nombre'] ?? '');
    $obraSocial = $data['id_obra_social'] ?? null;

    if ($dni === '' || $nombre === '') {
        apiError('dni y nombre son campos obligatorios', 422);
    }

    $stmt = $pdo->prepare('INSERT INTO pacientes (dni, nombre, id_obra_social, activo) VALUES (?, ?, ?, 1)');
    $stmt->execute([$dni, $nombre, $obraSocial ?: null]);

    apiResponse(['message' => 'Paciente creado', 'id' => (int)$pdo->lastInsertId()], 201);
}

/**
 * Actualiza los datos de un paciente existente.
 */
function updatePaciente(PDO $pdo, int $id): void {
    $data = getJsonInput();
    $dni = trim($data['dni'] ?? '');
    $nombre = trim($data['nombre'] ?? '');
    $obraSocial = $data['id_obra_social'] ?? null;

    if ($dni === '' || $nombre === '') {
        apiError('dni y nombre son campos obligatorios', 422);
    }

    $stmt = $pdo->prepare('UPDATE pacientes SET dni = ?, nombre = ?, id_obra_social = ? WHERE id = ?');
    $stmt->execute([$dni, $nombre, $obraSocial ?: null, $id]);

    if ($stmt->rowCount() === 0) {
        apiError('Paciente no encontrado o sin cambios', 404);
    }

    apiResponse(['message' => 'Paciente actualizado']);
}

/**
 * Realiza una eliminación lógica (soft delete) de un paciente.
 */
function deletePaciente(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare('UPDATE pacientes SET activo = 0 WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        apiError('Paciente no encontrado', 404);
    }

    apiResponse(['message' => 'Paciente eliminado (soft delete)']);
}

/**
 * Restaura un paciente que ha sido marcado como inactivo (borrado lógico).
 */
function restorePaciente(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare('UPDATE pacientes SET activo = 1 WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        apiError('Paciente no encontrado o ya activo', 404);
    }

    apiResponse(['message' => 'Paciente restaurado con éxito']);
}

/**
 * Registra un nuevo triage para un paciente.
 */
function createTriage(PDO $pdo): void {
    $data = getJsonInput();
    $nombrePaciente = trim($data['nombre_paciente'] ?? '');
    $nivelGravedad = $data['nivel_gravedad'] ?? null;
    $observaciones = trim($data['observaciones'] ?? '');

    if ($nombrePaciente === '' || $nivelGravedad === null) {
        apiError('nombre_paciente y nivel_gravedad son obligatorios', 422);
    }

    // Buscar paciente por nombre o crearlo si no existe
    $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE nombre = ? AND activo = 1");
    $stmt->execute([$nombrePaciente]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    $idPaciente = null;
    if ($paciente) {
        $idPaciente = $paciente['id'];
    } else {
        // Si no existe, crearlo (con DNI vacío por ahora, se podría pedir en el frontend)
        $stmt = $pdo->prepare("INSERT INTO pacientes (nombre, dni, activo) VALUES (?, ?, 1)");
        $stmt->execute([$nombrePaciente, 'PENDIENTE-' . uniqid()]); // Placeholder DNI
        $idPaciente = $pdo->lastInsertId();
    }

    $sql = "INSERT INTO triages (id_paciente, nivel_gravedad, observaciones, fecha) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idPaciente, $nivelGravedad, $observaciones]);

    apiResponse(['message' => 'Triage registrado correctamente', 'id_triage' => (int)$pdo->lastInsertId(), 'id_paciente' => (int)$idPaciente], 201);
}

/**
 * Lista las prescripciones médicas según su estado.
 */
function listPrescripciones(PDO $pdo): void {
    $estado = $_GET['estado'] ?? 'activa';

    $sql = "SELECT p.*, pac.nombre AS paciente_nombre, m.nombre AS medico_nombre
            FROM prescripciones p
            LEFT JOIN pacientes pac ON p.id_paciente = pac.id
            LEFT JOIN medicos m ON p.id_medico = m.id
            WHERE p.estado = ?
            ORDER BY p.fecha_emision DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$estado]);
    apiResponse(['data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

/**
 * Obtiene el detalle de una prescripción específica.
 */
function getPrescripcion(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare(
        "SELECT p.*, pac.nombre AS paciente_nombre, m.nombre AS medico_nombre
         FROM prescripciones p
         LEFT JOIN pacientes pac ON p.id_paciente = pac.id
         LEFT JOIN medicos m ON p.id_medico = m.id
         WHERE p.id = ?"
    );
    $stmt->execute([$id]);
    $prescripcion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prescripcion) {
        apiError('Prescripción no encontrada', 404);
    }

    apiResponse(['data' => $prescripcion]);
}

/**
 * Crea una nueva prescripción electrónica validando existencia de médico y paciente.
 */
function createPrescripcion(PDO $pdo): void {
    $data = getJsonInput();
    $idPaciente = $data['id_paciente'] ?? null;
    $idMedico = $data['id_medico'] ?? null;
    $medicamentos = $data['medicamentos'] ?? [];
    $indicaciones = trim($data['indicaciones'] ?? '');
    $fechaVencimiento = $data['fecha_vencimiento'] ?? date('Y-m-d', strtotime('+30 days'));

    if (!$idPaciente || !$idMedico || empty($medicamentos)) {
        apiError('id_paciente, id_medico y medicamentos son obligatorios', 422);
    }

    // Verificar que paciente y medico existen
    $stmt = $pdo->prepare('SELECT id FROM pacientes WHERE id = ? AND activo = 1');
    $stmt->execute([$idPaciente]);
    if (!$stmt->fetch()) {
        apiError('Paciente no encontrado o inactivo', 404);
    }

    $stmt = $pdo->prepare('SELECT id FROM medicos WHERE id = ? AND activo = 1');
    $stmt->execute([$idMedico]);
    if (!$stmt->fetch()) {
        apiError('Médico no encontrado o inactivo', 404);
    }

    $medicamentosJson = json_encode($medicamentos);
    $qrCode = 'QR-' . uniqid(); // Simular QR
    $firmaDigital = 'FIRMA-' . $idMedico . '-' . time(); // Simular firma

    $stmt = $pdo->prepare('INSERT INTO prescripciones (id_paciente, id_medico, medicamentos, indicaciones, fecha_vencimiento, qr_code, firma_digital) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$idPaciente, $idMedico, $medicamentosJson, $indicaciones, $fechaVencimiento, $qrCode, $firmaDigital]);

    apiResponse(['message' => 'Prescripción creada', 'id' => (int)$pdo->lastInsertId()], 201);
}

/**
 * Actualiza el estado de una prescripción (ej: a 'dispensada').
 */
function updatePrescripcion(PDO $pdo, int $id): void {
    $data = getJsonInput();
    $estado = $data['estado'] ?? null;

    if (!$estado) {
        apiError('estado es obligatorio', 422);
    }

    $stmt = $pdo->prepare('UPDATE prescripciones SET estado = ? WHERE id = ?');
    $stmt->execute([$estado, $id]);

    if ($stmt->rowCount() === 0) {
        apiError('Prescripción no encontrada', 404);
    }

    apiResponse(['message' => 'Prescripción actualizada']);
}

/**
 * Elimina físicamente una prescripción de la base de datos.
 */
function deletePrescripcion(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare('DELETE FROM prescripciones WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        apiError('Prescripción no encontrada', 404);
    }

    apiResponse(['message' => 'Prescripción eliminada correctamente']);
}

/**
 * Lista los médicos activos registrados.
 */
function listMedicos(PDO $pdo): void {
    $stmt = $pdo->prepare('SELECT * FROM medicos WHERE activo = 1 ORDER BY nombre ASC');
    $stmt->execute();
    apiResponse(['data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

/**
 * Registra un nuevo médico en el sistema.
 */
function createMedico(PDO $pdo): void {
    $data = getJsonInput();
    $nombre = trim($data['nombre'] ?? '');
    $matricula = trim($data['matricula'] ?? '');
    $especialidad = trim($data['especialidad'] ?? '');

    if ($nombre === '' || $matricula === '') {
        apiError('nombre y matricula son obligatorios', 422);
    }

    $stmt = $pdo->prepare('INSERT INTO medicos (nombre, matricula, especialidad, activo) VALUES (?, ?, ?, 1)');
    $stmt->execute([$nombre, $matricula, $especialidad]);

    apiResponse(['message' => 'Médico creado', 'id' => (int)$pdo->lastInsertId()], 201);
}

// --- Sistema de Ruteo Manual de la API ---
$rutaApi = isset($ruta) ? $ruta : trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', trim($rutaApi, '/'));

// Validación básica de ruta
if (count($segments) < 2 || $segments[0] !== 'api') {
    apiError('Ruta API inválida', 404);
}

$resource = $segments[1] ?? null;
$id = isset($segments[2]) ? (int)$segments[2] : null;
$method = $_SERVER['REQUEST_METHOD'];

// Routing para Pacientes
if ($resource === 'pacientes') {
    switch ($method) {
        case 'GET':
            if ($id !== null && $id > 0) {
                getPaciente($pdo, $id);
            }
            listPacientes($pdo);
            break;
        case 'POST':
            createPaciente($pdo);
            break;
        case 'PUT':
        case 'PATCH':
            if ($id === null || $id <= 0) {
                apiError('ID de paciente requerido', 400);
            }
            if ($action === 'restore') {
                restorePaciente($pdo, $id);
            } else {
            updatePaciente($pdo, $id);
            }
            break;
        case 'DELETE':
            if ($id === null || $id <= 0) {
                apiError('ID de paciente requerido', 400);
            }
            deletePaciente($pdo, $id);
            break;
        default:
            apiError('Método no permitido', 405);
    }
}

// Routing para Triage
if ($resource === 'triage') {
    switch ($method) {
        case 'POST':
            createTriage($pdo);
            break;
        default:
            apiError('Método no permitido', 405);
    }
}

// Routing para Prescripciones
if ($resource === 'prescripciones') {
    switch ($method) {
        case 'GET':
            if ($id !== null && $id > 0) {
                getPrescripcion($pdo, $id);
            }
            listPrescripciones($pdo);
            break;
        case 'POST':
            createPrescripcion($pdo);
            break;
        case 'PUT':
        case 'PATCH':
            if ($id === null || $id <= 0) {
                apiError('ID de prescripción requerido', 400);
            }
            updatePrescripcion($pdo, $id);
            break;
        case 'DELETE':
            if ($id === null || $id <= 0) {
                apiError('ID de prescripción requerido', 400);
            }
            deletePrescripcion($pdo, $id);
            break;
        default:
            apiError('Método no permitido', 405);
    }
}

// Routing para Médicos
if ($resource === 'medicos') {
    switch ($method) {
        case 'GET':
            listMedicos($pdo);
            break;
        case 'POST':
            createMedico($pdo);
            break;
        default:
            apiError('Método no permitido', 405);
    }
}

// Routing para Chat
if ($resource === 'chat') {
    if ($method === 'OPTIONS') {
        apiResponse(['status' => 'ok']);
    }
    if ($method !== 'POST') {
        apiError('Método no permitido', 405);
    }
    chatAssistant($pdo);
}

// Routing para Documentación
if ($resource === 'docs' || $resource === 'openapi.json') {
    if ($method !== 'GET') {
        apiError('Método no permitido', 405);
    }
    apiDocs();
}

apiError('Recurso API no encontrado', 404);
