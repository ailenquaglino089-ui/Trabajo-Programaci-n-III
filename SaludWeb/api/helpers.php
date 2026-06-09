<?php
function respond_json(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function respond_ok($data): void {
    respond_json(['ok' => true, 'data' => $data], 200);
}

function respond_error($errors, int $status = 400): void {
    if (!is_array($errors)) {
        $errors = [(string) $errors];
    }
    respond_json(['ok' => false, 'errors' => array_values($errors)], $status);
}

function respond_method_not_allowed(string $allowed = 'GET'): void {
    header('Allow: ' . $allowed);
    respond_error("Método no permitido. Usa $allowed.", 405);
}
