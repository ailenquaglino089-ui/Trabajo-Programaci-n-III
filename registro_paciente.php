<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SaludWEB - Registro</title>
    <style>
        body { margin: 0; font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: linear-gradient(135deg, #eef2ff 0%, #ffffff 100%); color: #0f172a; }
        .page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 16px; }
        .card { width: 100%; max-width: 520px; background: #ffffff; border: 1px solid #dbeafe; border-radius: 20px; box-shadow: 0 24px 70px rgba(15, 23, 42, 0.12); padding: 34px; }
        h1 { font-size: 1.95rem; margin: 0 0 22px; line-height: 1.1; }
        h1 span { display: inline-block; background: #e0f2fe; color: #1d4ed8; padding: 6px 12px; border-radius: 999px; font-weight: 800; margin-right: 8px; }
        p { margin: 0 0 18px; }
        label { display: block; margin-bottom: 8px; font-weight: 700; color: #1e40af; letter-spacing: 0.02em; text-transform: uppercase; }
        input, select { width: 100%; border: 1px solid #c7d2fe; border-radius: 12px; padding: 12px 14px; font-size: 0.98rem; color: #0f172a; background: #f8fbff; }
        input:focus, select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); }
        button { width: 100%; padding: 14px 16px; border: none; border-radius: 12px; background: linear-gradient(135deg, #2563eb 0%, #9333ea 100%); color: #ffffff; font-size: 1rem; font-weight: 800; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease; box-shadow: 0 12px 24px rgba(59, 130, 246, 0.18); }
        button:hover { transform: translateY(-1px); box-shadow: 0 16px 28px rgba(59, 130, 246, 0.22); }
        .link-back { display: inline-flex; align-items: center; color: #2563eb; text-decoration: none; font-weight: 700; transition: color 0.2s ease; }
        .link-back:hover { color: #1d4ed8; }
        .link-back::before { content: '←'; margin-right: 8px; }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <h1><span>SaludWEB</span>Registro de Paciente Nuevo</h1>
            <form action="procesar_registro.php" method="POST">
                <p>
                    <label for="dni">DNI del Paciente:</label>
                    <input type="number" id="dni" name="dni" required>
                </p>

                <p>
                    <label for="nombre">Nombre Completo:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </p>

                <p>
                    <label for="obra">Obra Social:</label>
                    <select id="obra" name="id_obra_social">
                        <option value="1">Particular</option>
                        <option value="2">OSDE</option>
                        <option value="3">PAMI</option>
                    </select>
                </p>

                <button type="submit">Guardar y Continuar al Triage</button>
            </form>

            <p style="margin-top:22px; text-align:center;">
                <a href="lista" class="link-back">Volver al Dashboard</a>
            </p>
        </div>
    </div>
</body>
</html>