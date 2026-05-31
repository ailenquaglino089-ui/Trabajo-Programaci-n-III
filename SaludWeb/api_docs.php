<?php
// SaludWEB/api_docs.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación API | SaludWEB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.27.0/swagger-ui.min.css" integrity="sha512-g33hTHuZq4x+/1c57/DkmNDyLJpVNPhM+E7/J5oU05ws4+EqcFWoK77ObCgCZIH1QzHH0Y5K9FQXi6fdYg+kVg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; }
        .topbar { background: #0b79d0; color: white; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; }
        .topbar a { color: white; text-decoration: none; font-weight: bold; }
        .topbar .info { font-size: 0.95rem; }
        #swagger-ui { margin-top: 0; }
    </style>
</head>
<body>
    <div class="topbar">
        <div>
            <strong>SaludWEB API Docs</strong>
            <span class="info">Documentación generada automáticamente</span>
        </div>
        <div>
            <a href="lista_pacientes.php">Volver al dashboard</a>
        </div>
    </div>
    <div id="swagger-ui"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.27.0/swagger-ui-bundle.min.js" integrity="sha512-GXC3n6HSN1lPdp57PA5gGjDoW+6tJwB+ZmIu0zPGriFjR9Vy9U++3B3kNIdt4CL5Oo1uGgB5oD+KKYssmSZdWw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        const basePath = window.location.pathname.replace(/\/api_docs\.php$/, '');
        const apiDocUrl = window.location.origin + basePath + '/api/openapi.json';

        SwaggerUIBundle({
            url: apiDocUrl,
            dom_id: '#swagger-ui',
            presets: [SwaggerUIBundle.presets.apis],
            layout: 'BaseLayout',
            docExpansion: 'list',
            defaultModelsExpandDepth: -1
        });
    </script>
</body>
</html>
