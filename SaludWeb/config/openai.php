<?php
// SaludWEB/config/openai.php
// Configuración de OpenAI para el proyecto.
// Define estas variables de entorno en tu servidor o entorno local:
//   OPENAI_API_KEY   -> clave secreta de OpenAI
//   OPENAI_MODEL     -> modelo a usar, por ejemplo gpt-3.5-turbo
//   OPENAI_BASE_URL  -> URL base de la API, normalmente https://api.openai.com/v1
return [
    'api_key' => getenv('OPENAI_API_KEY') ?: '',
    'model' => getenv('OPENAI_MODEL') ?: 'gpt-3.5-turbo',
    'base_url' => getenv('OPENAI_BASE_URL') ?: 'https://api.openai.com/v1'
];
