<?php
// SaludWEB/configuracion.php
// Página de configuración con instrucciones para credenciales y variables de entorno.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración | SaludWEB</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fb; margin: 0; padding: 0; }
        .container { max-width: 860px; margin: 32px auto; padding: 0 20px; }
        .card { background: #fff; border-radius: 18px; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08); padding: 28px; }
        h1 { margin-top: 0; color: #0f172a; }
        h2 { margin-top: 32px; color: #1e293b; }
        p, li { color: #475569; line-height: 1.7; }
        code { background: #f8fafc; padding: 2px 6px; border-radius: 6px; font-size: 0.95rem; }
        ul { margin-left: 20px; }
        .note { background: #eef2ff; border-left: 4px solid #3b82f6; padding: 14px 16px; border-radius: 12px; margin-top: 24px; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #2563eb; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <a href="lista_pacientes.php" class="back-link">← Volver al dashboard</a>
            <h1>Configuración</h1>
            <p>En esta sección encontrarás las credenciales y variables que usa el proyecto.</p>

            <h2>1. Base de datos</h2>
            <p>El proyecto carga los datos de la base de datos desde las variables de entorno declaradas en <code>db.php</code>. Las claves son:</p>
            <ul>
                <li><code>DB_HOST</code> - servidor de base de datos, normalmente <code>localhost</code>.</li>
                <li><code>DB_NAME</code> - nombre de la base de datos, por defecto <code>pacientes</code>.</li>
                <li><code>DB_USER</code> - usuario de la base de datos, por defecto <code>root</code>.</li>
                <li><code>DB_PASS</code> - contraseña del usuario de base de datos.</li>
            </ul>
            <p>Si no defines estas variables, el proyecto usará los valores internos por defecto.</p>

            <h2>2. OpenAI / Chat</h2>
            <p>Para que el asistente de <strong>chat</strong> funcione con OpenAI, se usan estas variables:</p>
            <ul>
                <li><code>OPENAI_API_KEY</code> - tu clave secreta de OpenAI.</li>
                <li><code>OPENAI_MODEL</code> - modelo a usar, por ejemplo <code>gpt-3.5-turbo</code> o <code>gpt-4</code>.</li>
                <li><code>OPENAI_BASE_URL</code> - URL base de la API, normalmente <code>https://api.openai.com/v1</code>.</li>
            </ul>
            <p>Estos valores se cargan en <code>config/openai.php</code>. Si no pones una clave, el chat mostrará advertencias y no podrá llamar a OpenAI.</p>

            <div class="note">
                <strong>Recomendación:</strong> guarda las credenciales en variables de entorno o configuración del servidor, nunca en archivos públicos accesibles desde el navegador.
            </div>

            <h2>3. Ejemplo de configuración</h2>
            <p>En Windows con XAMPP, puedes crear variables de entorno del sistema o usar un archivo de configuración local protegido. En sistemas Linux/Mac, puedes exportarlas antes de iniciar Apache.</p>
            <pre><code>DB_HOST=localhost
DB_NAME=pacientes
DB_USER=root
DB_PASS=
OPENAI_API_KEY=tu_clave_openai_aqui
OPENAI_MODEL=gpt-3.5-turbo
OPENAI_BASE_URL=https://api.openai.com/v1</code></pre>

            <h2>4. Archivo <code>config/openai.php</code></h2>
            <p>Ese archivo lee las variables de entorno y devuelve la configuración al proyecto. Así puedes cambiar el modelo o usar un proxy sin modificar la lógica del chat.</p>
        </div>
    </div>
</body>
</html>
