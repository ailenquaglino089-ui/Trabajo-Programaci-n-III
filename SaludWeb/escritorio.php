<?php
// SaludWEB/escritorio.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escritorio | SaludWEB</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #eef2f6; margin: 0; padding: 24px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
        .header a { text-decoration: none; color: #fff; background: #007bff; padding: 10px 18px; border-radius: 12px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 20px; }
        .card { background: #fff; border-radius: 22px; padding: 24px; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06); }
        .card h2 { margin-top: 0; font-size: 1.3rem; margin-bottom: 10px; }
        .card p { color: #475569; line-height: 1.6; }
        .card a { display: inline-block; margin-top: 16px; color: #fff; background: #007bff; padding: 10px 16px; border-radius: 12px; text-decoration: none; }
        .highlight { background: #f8fafc; border: 1px solid #e2e8f0; }
        .stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-bottom: 24px; }
        .stats .item { background: #fff; border-radius: 18px; padding: 18px 22px; box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06); }
        .item h3 { margin: 0 0 8px; color: #334155; }
        .item p { font-size: 1.6rem; margin: 0; color: #0f172a; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Escritorio de SaludWEB</h1>
                <p>Panel de acceso rápido a las funciones más importantes del sistema.</p>
            </div>
            <a href="lista_pacientes.php">Volver al Dashboard</a>
        </div>

        <div class="stats">
            <div class="item">
                <h3>Acceso rápido</h3>
                <p>Abre la lista de pacientes, registra uno nuevo o revisa la papelera con un clic.</p>
            </div>
            <div class="item">
                <h3>IA y ayuda</h3>
                <p>Usa el asistente inteligente para consultar rutas, datos o funcionamiento del proyecto.</p>
            </div>
            <div class="item">
                <h3>Configuración</h3>
                <p>La clave de OpenAI se puede configurar en <code>config/openai.php</code> o vía entorno.</p>
            </div>
        </div>

        <div class="grid">
            <div class="card highlight">
                <h2>📋 Lista de pacientes</h2>
                <p>Revisa los pacientes activos, su nivel de gravedad y el estado general del sistema.</p>
                <a href="lista_pacientes.php">Ir a lista</a>
            </div>

            <div class="card highlight">
                <h2>➕ Registrar nuevo paciente</h2>
                <p>Crea pacientes nuevos rápidamente con DNI, nombre y obra social.</p>
                <a href="/prog3-clase2/registro">Registrar</a>
            </div>

            <div class="card highlight">
                <h2>🗑️ Ver papelera</h2>
                <p>Restaura o elimina permanentemente pacientes que hayan sido dados de baja.</p>
                <a href="papelera.php">Ir a papelera</a>
            </div>

            <div class="card">
                <h2>💬 Asistente AI</h2>
                <p>Envía preguntas sobre el proyecto o la API directamente desde la interfaz web.</p>
                <a href="chat.php">Abrir asistente</a>
            </div>

            <div class="card">
                <h2>⚙️ Configuración OpenAI</h2>
                <p>La clave se puede fijar en el archivo <code>config/openai.php</code> para que el asistente use OpenAI.</p>
            </div>

            <div class="card">
                <h2>🧠 API del proyecto</h2>
                <p>Accede a las rutas REST para pacientes o utiliza la ruta de chat para ayuda inteligente.</p>
                <a href="api_docs.php">Ver API</a>
            </div>
            <div class="card">
                <h2>📄 Documentación API</h2>
                <p>Ver la documentación generada automáticamente para los endpoints de la API.</p>
                <a href="api_docs.php">Abrir Docs</a>
            </div>
        </div>
    </div>
</body>
</html>
