<?php
// SaludWEB/notificaciones.php - Centro de Notificaciones
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones | SaludWEB</title>
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; --text: #1e293b; --success: #10b981; --warning: #f59e0b; --danger: #e11d48; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 20px; margin: 0; }
        .container { max-width: 900px; margin: 0 auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .back-link { text-decoration: none; color: var(--primary); font-weight: 800; font-size: 14px; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px; background: white; border: 2px solid var(--primary); transition: 0.3s; }
        .back-link:hover { background: var(--primary); color: white; }
        
        .title-section h1 { margin: 0; font-size: 28px; color: var(--text); }
        .title-section p { margin: 5px 0 0 0; color: #64748b; }
        
        .notification { background: white; border-radius: 12px; padding: 20px; margin-bottom: 16px; border-left: 4px solid #cbd5e1; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); }
        .notification.important { border-left-color: var(--danger); background: #fff5f5; }
        .notification.warning { border-left-color: var(--warning); background: #fffbeb; }
        .notification.success { border-left-color: var(--success); background: #f0fdf4; }
        
        .notification-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .notification-title { font-weight: 800; font-size: 16px; color: var(--text); }
        .notification-date { font-size: 12px; color: #64748b; }
        .notification-content { color: #475569; line-height: 1.6; }
        
        .empty-state { text-align: center; padding: 40px; background: white; border-radius: 12px; }
        .empty-icon { font-size: 48px; margin-bottom: 16px; }
        .empty-text { font-size: 16px; color: #64748b; margin: 0; }
        
        .filter-buttons { display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap; }
        .filter-btn { padding: 10px 16px; border-radius: 8px; border: 2px solid #cbd5e1; background: white; color: var(--text); cursor: pointer; font-weight: 700; transition: 0.3s; }
        .filter-btn.active { background: var(--primary); color: white; border-color: var(--primary); }
        .filter-btn:hover { border-color: var(--primary); }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="title-section">
            <h1>🔔 Notificaciones</h1>
            <p>Gestiona tus avisos y recordatorios</p>
        </div>
        <a href="mis_rx.php" class="back-link">← Volver</a>
    </div>
    
    <div class="filter-buttons">
        <button class="filter-btn active" onclick="filterNotifications('todas')">Todas</button>
        <button class="filter-btn" onclick="filterNotifications('importantes')">Importantes</button>
        <button class="filter-btn" onclick="filterNotifications('recordatorios')">Recordatorios</button>
        <button class="filter-btn" onclick="filterNotifications('sistema')">Sistema</button>
    </div>
    
    <div id="notifications-list">
        <div class="notification important">
            <div class="notification-header">
                <span class="notification-title">⚠️ Prescripción por vencer</span>
                <span class="notification-date">Hace 2 horas</span>
            </div>
            <div class="notification-content">Tu prescripción Rx-2024-001 vence en 3 días. Por favor, renuévala antes de que caduque.</div>
        </div>
        
        <div class="notification warning">
            <div class="notification-header">
                <span class="notification-title">📋 Recordatorio: Toma de medicamentos</span>
                <span class="notification-date">Hace 4 horas</span>
            </div>
            <div class="notification-content">Recuerda tomar tu medicación según lo indicado. Es importante mantener la continuidad del tratamiento.</div>
        </div>
        
        <div class="notification success">
            <div class="notification-header">
                <span class="notification-title">✓ Prescripción dispensada</span>
                <span class="notification-date">Hace 1 día</span>
            </div>
            <div class="notification-content">Tu prescripción Rx-2024-002 ha sido dispensada exitosamente en la farmacia.</div>
        </div>
        
        <div class="notification">
            <div class="notification-header">
                <span class="notification-title">🔔 Nuevo mensaje de soporte</span>
                <span class="notification-date">Hace 1 día</span>
            </div>
            <div class="notification-content">Tu consulta sobre prestaciones ha sido respondida. Haz clic para ver la respuesta.</div>
        </div>
        
        <div class="notification">
            <div class="notification-header">
                <span class="notification-title">📊 Reporte de actividad</span>
                <span class="notification-date">Hace 3 días</span>
            </div>
            <div class="notification-content">Tu reporte mensual de salud está listo para descargar desde el panel de informes.</div>
        </div>
    </div>
    
    <script>
        function filterNotifications(type) {
            // Aquí iría la lógica para filtrar notificaciones
            console.log('Filtrar por:', type);
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>
</div>
</body>
</html>
