<?php
// SaludWEB/mis_rx.php - Centro de control de prescripciones y servicios
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Rx | SaludWEB</title>
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; --text: #1e293b; --danger: #e11d48; --warning: #f59e0b; --success: #10b981; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 20px; margin: 0; }
        .dashboard { max-width: 1200px; margin: 0 auto; }
        
        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 15px; flex-wrap: wrap; }
        .logo-container { display: flex; align-items: center; gap: 10px; }
        .logo-box { background: var(--primary); color: white; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 24px; font-weight: bold; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        .logo-text { font-size: 28px; font-weight: 800; letter-spacing: -1px; }
        .logo-pro { background: #fee2e2; color: var(--danger); padding: 2px 8px; border-radius: 6px; font-size: 14px; vertical-align: middle; margin-left: 5px; }
        
        .header-actions { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        .back-link, .config-link, .logout-link { text-decoration: none; color: var(--primary); font-weight: 800; font-size: 14px; display: flex; align-items: center; gap: 8px; transition: 0.3s; padding: 10px 20px; border-radius: 12px; background: white; border: 2px solid var(--primary); box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15); }
        .back-link:hover { background: var(--primary); color: white; transform: translateY(-2px); }
        .config-link:hover { background: var(--primary); color: white; transform: translateY(-2px); }
        .logout-link { border-color: #ef4444; color: #ef4444; }
        .logout-link:hover { background: #ef4444; color: white; }
        
        .subtitle { color: #64748b; font-size: 16px; font-weight: 600; margin-top: 5px; }
        
        .grid-menu { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .menu-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); border: 2px solid #e2e8f0; text-decoration: none; color: var(--text); transition: 0.3s; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 12px; }
        .menu-card:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12); border-color: var(--primary); }
        .menu-card-icon { font-size: 40px; }
        .menu-card-title { font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
        .menu-card-desc { font-size: 12px; color: #64748b; line-height: 1.4; }
        
        .content-section { background: white; border-radius: 16px; padding: 28px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); margin-bottom: 24px; }
        .section-title { font-size: 24px; font-weight: 800; margin: 0 0 20px 0; color: #0f172a; display: flex; align-items: center; gap: 12px; }
        
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 16px; }
        .info-card { background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 16px; }
        .info-card-label { font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.3px; margin-bottom: 6px; }
        .info-card-value { font-size: 18px; font-weight: 800; color: var(--text); }
        
        .button-group { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 16px; }
        .btn { display: inline-block; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 800; text-decoration: none; text-align: center; transition: 0.3s; font-size: 14px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #3730a3; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(79, 70, 229, 0.3); }
        .btn-secondary { background: #f1f5f9; color: #1e293b; border: 2px solid #cbd5e1; }
        .btn-secondary:hover { background: #e2e8f0; }
        
        .social-icons { display: flex; gap: 12px; margin-top: 16px; }
        .social-link { display: flex; align-items: center; justify-content: center; width: 45px; height: 45px; border-radius: 50%; text-decoration: none; font-size: 22px; transition: 0.3s; border: 2px solid #e2e8f0; }
        .whatsapp-link { background: #25d366; color: white; border-color: #25d366; }
        .whatsapp-link:hover { transform: scale(1.1); box-shadow: 0 6px 15px rgba(37, 211, 102, 0.3); }
        .gmail-link { background: #ea4335; color: white; border-color: #ea4335; }
        .gmail-link:hover { transform: scale(1.1); box-shadow: 0 6px 15px rgba(234, 67, 53, 0.3); }
        
        .alert { padding: 16px 20px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid; }
        .alert-info { background: #eef2ff; border-color: #3b82f6; color: #1e40af; }
        
        @media (max-width: 768px) {
            .header-top { flex-direction: column; align-items: flex-start; }
            .header-actions { width: 100%; }
            .grid-menu { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .menu-card { padding: 16px; }
            .menu-card-icon { font-size: 32px; }
            .content-section { padding: 20px; }
        }
        
        @media (max-width: 480px) {
            body { padding: 12px; }
            .grid-menu { grid-template-columns: 1fr; }
            .section-title { font-size: 18px; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="dashboard">
    <!-- Header -->
    <div class="header-top">
        <div>
            <div class="logo-container">
                <div class="logo-box">✚</div>
                <div class="logo-text">SaludWeb<span class="logo-pro">PRO</span></div>
            </div>
            <p class="subtitle">Centro de Control - Mis Prescripciones y Servicios</p>
        </div>
        <div class="header-actions">
            <a href="lista_pacientes.php" class="back-link">← Volver</a>
            <a href="configuracion.php" class="config-link">⚙️ Ajustes</a>
            <a href="logout.php" class="logout-link">🔒 Salir</a>
        </div>
    </div>
    
    <!-- Sección Mis Rx -->
    <div class="content-section">
        <h2 class="section-title">💊 Mis Rx</h2>
        <div class="grid-menu">
            <a href="lista_prescripciones.php" class="menu-card">
                <div class="menu-card-icon">📄</div>
                <div class="menu-card-title">Prescripciones</div>
                <div class="menu-card-desc">Ver y gestionar mis recetas electrónicas</div>
            </a>
            
            <a href="notificaciones.php" class="menu-card">
                <div class="menu-card-icon">🔔</div>
                <div class="menu-card-title">Notificaciones</div>
                <div class="menu-card-desc">Revisar avisos y recordatorios</div>
            </a>
            
            <a href="prestaciones.php" class="menu-card">
                <div class="menu-card-icon">🏥</div>
                <div class="menu-card-title">Prestaciones</div>
                <div class="menu-card-desc">Consultar servicios disponibles</div>
            </a>
            
            <a href="buscador_farmacias.php" class="menu-card">
                <div class="menu-card-icon">🏪</div>
                <div class="menu-card-title">Buscador de Farmacias</div>
                <div class="menu-card-desc">Localizar farmacias cercanas</div>
            </a>
        </div>
    </div>
    
    <!-- Sección de Contacto -->
    <div class="content-section">
        <h2 class="section-title">📞 Contacto y Soporte</h2>
        <p style="color: #64748b; margin: 0 0 16px 0; line-height: 1.6;">¿Necesitas ayuda? Puedes contactarnos a través de WhatsApp, Gmail o acceder a nuestras Preguntas Frecuentes.</p>
        <div class="social-icons">
            <a href="https://wa.me/541234567890" target="_blank" class="social-link whatsapp-link" title="Contactarnos por WhatsApp">
                <span>💬</span>
            </a>
            <a href="mailto:soporte@saludweb.com" class="social-link gmail-link" title="Enviar correo">
                <span>✉️</span>
            </a>
        </div>
        <div style="margin-top: 16px;">
            <a href="preguntas_frecuentes.php" class="btn btn-primary">? Preguntas Frecuentes</a>
        </div>
    </div>
    
    <!-- Información de Utilidad -->
    <div class="content-section">
        <h2 class="section-title">ℹ️ Información Útil</h2>
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-label">Estado del Sistema</div>
                <div class="info-card-value" style="color: var(--success);">✓ Activo</div>
            </div>
            <div class="info-card">
                <div class="info-card-label">Última Sincronización</div>
                <div class="info-card-value"><?php echo date('d/m/Y H:i'); ?></div>
            </div>
            <div class="info-card">
                <div class="info-card-label">Versión</div>
                <div class="info-card-value">3.0</div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
