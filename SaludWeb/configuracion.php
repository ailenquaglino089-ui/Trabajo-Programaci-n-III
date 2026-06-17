<?php
// SaludWEB/configuracion.php
// Página de Ajustes y Configuración del usuario
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajustes | SaludWEB</title>
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; --text: #1e293b; --success: #10b981; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 20px; margin: 0; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .logo-container { display: flex; align-items: center; gap: 10px; }
        .logo-box { background: var(--primary); color: white; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 20px; font-weight: bold; }
        .logo-text { font-size: 24px; font-weight: 800; }
        
        .header-actions { display: flex; gap: 12px; }
        .back-link { text-decoration: none; color: var(--primary); font-weight: 800; font-size: 14px; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px; background: white; border: 2px solid var(--primary); transition: 0.3s; }
        .back-link:hover { background: var(--primary); color: white; }
        
        .nav-menu { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin-bottom: 30px; }
        .nav-item { padding: 12px 16px; border-radius: 10px; background: white; text-decoration: none; color: var(--text); font-weight: 700; text-align: center; transition: 0.3s; border: 2px solid #e2e8f0; }
        .nav-item:hover, .nav-item.active { background: var(--primary); color: white; border-color: var(--primary); }
        
        .content { background: white; border-radius: 16px; padding: 28px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); }
        .section { margin-bottom: 32px; }
        .section-title { font-size: 20px; font-weight: 800; margin: 0 0 16px 0; display: flex; align-items: center; gap: 10px; }
        .section-desc { color: #64748b; margin: 0 0 16px 0; }
        
        .settings-group { background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 12px; border: 2px solid #e2e8f0; }
        .settings-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; }
        .settings-label { font-weight: 700; color: var(--text); }
        .settings-value { color: #64748b; font-size: 14px; }
        
        .btn { display: inline-block; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 800; text-decoration: none; text-align: center; transition: 0.3s; font-size: 14px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #3730a3; transform: translateY(-2px); }
        .btn-secondary { background: #f1f5f9; color: #1e293b; border: 2px solid #cbd5e1; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        
        .contact-icons { display: flex; gap: 12px; margin-top: 16px; }
        .social-link { display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; border-radius: 50%; text-decoration: none; font-size: 24px; transition: 0.3s; border: 2px solid #e2e8f0; }
        .whatsapp-link { background: #25d366; color: white; border-color: #25d366; }
        .whatsapp-link:hover { transform: scale(1.15); box-shadow: 0 6px 15px rgba(37, 211, 102, 0.3); }
        .gmail-link { background: #ea4335; color: white; border-color: #ea4335; }
        .gmail-link:hover { transform: scale(1.15); box-shadow: 0 6px 15px rgba(234, 67, 53, 0.3); }
        
        .info-box { background: #eef2ff; border-left: 4px solid var(--primary); padding: 14px 16px; border-radius: 12px; }
        .info-box p { margin: 0; color: #1e40af; font-size: 14px; }
        
        @media (max-width: 768px) {
            .header { flex-direction: column; align-items: flex-start; }
            .nav-menu { grid-template-columns: repeat(2, 1fr); }
            .settings-row { flex-direction: column; align-items: flex-start; gap: 8px; }
            .content { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="logo-container">
            <div class="logo-box">⚙️</div>
            <div class="logo-text">Ajustes</div>
        </div>
        <div class="header-actions">
            <a href="lista_pacientes.php" class="back-link">← Home</a>
        </div>
    </div>
    
    <!-- Menú de Navegación -->
    <div class="nav-menu">
        <a href="lista_pacientes.php" class="nav-item">🏠 Home</a>
        <a href="lista_prescripciones.php" class="nav-item">📄 Prescripciones</a>
        <a href="prestaciones.php" class="nav-item">🏥 Prestaciones</a>
        <a href="notificaciones.php" class="nav-item">🔔 Notificaciones</a>
        <a href="configuracion.php" class="nav-item active">⚙️ Ajustes</a>
        <a href="cuenta.php" class="nav-item">👤 Cuenta</a>
        <a href="preguntas_frecuentes.php" class="nav-item">❓ FAQ</a>
        <a href="logout.php" class="nav-item" style="border-color:#ef4444; color:#ef4444;">🔒 Salir</a>
    </div>
    
    <!-- Contenido Principal -->
    <div class="content">
        
        <!-- Sección Notificaciones -->
        <div class="section">
            <h2 class="section-title">🔔 Notificaciones</h2>
            <p class="section-desc">Controla tus preferencias de notificaciones</p>
            
            <div class="settings-group">
                <div class="settings-row">
                    <span class="settings-label">Avisos de prescripciones</span>
                    <input type="checkbox" checked>
                </div>
            </div>
            
            <div class="settings-group">
                <div class="settings-row">
                    <span class="settings-label">Recordatorios de medicamentos</span>
                    <input type="checkbox" checked>
                </div>
            </div>
            
            <div class="settings-group">
                <div class="settings-row">
                    <span class="settings-label">Actualizaciones del sistema</span>
                    <input type="checkbox">
                </div>
            </div>
            
            <div class="settings-group">
                <div class="settings-row">
                    <span class="settings-label">Ofertas y promociones</span>
                    <input type="checkbox">
                </div>
            </div>
        </div>
        
        <!-- Sección Privacidad y Seguridad -->
        <div class="section">
            <h2 class="section-title">🔒 Privacidad y Seguridad</h2>
            <p class="section-desc">Protege tu información personal</p>
            
            <div class="settings-group">
                <div class="settings-row">
                    <span class="settings-label">Autenticación de dos factores</span>
                    <span class="settings-value">No activado</span>
                </div>
            </div>
            
            <button class="btn btn-primary" onclick="alert('Función de 2FA')">🔐 Activar 2FA</button>
            
            <div style="margin-top: 20px;">
                <div class="info-box">
                    <p><strong>✓ Tu información está protegida</strong> con encriptación de nivel industrial y cumple con normativas internacionales de privacidad.</p>
                </div>
            </div>
        </div>
        
        <!-- Sección Contacto y Soporte -->
        <div class="section">
            <h2 class="section-title">📞 Contacto y Soporte</h2>
            <p class="section-desc">¿Necesitas ayuda? Contáctanos a través de tus canales preferidos</p>
            
            <div class="contact-icons">
                <a href="https://wa.me/541234567890" target="_blank" class="social-link whatsapp-link" title="Contactarnos por WhatsApp">
                    <span>💬</span>
                </a>
                <a href="mailto:soporte@saludweb.com" class="social-link gmail-link" title="Enviar correo">
                    <span>✉️</span>
                </a>
            </div>
            
            <div style="margin-top: 20px;">
                <p style="color: #64748b; font-size: 14px;"><strong>WhatsApp:</strong> +54 9 11 1234-5678</p>
                <p style="color: #64748b; font-size: 14px;"><strong>Email:</strong> soporte@saludweb.com</p>
                <p style="color: #64748b; font-size: 14px;"><strong>Horario:</strong> Lun-Vie 8:00-20:00 hs</p>
            </div>
        </div>
        
        <!-- Sección Ayuda -->
        <div class="section">
            <h2 class="section-title">❓ Centro de Ayuda</h2>
            <p class="section-desc">Accede a documentación y preguntas frecuentes</p>
            
            <a href="preguntas_frecuentes.php" class="btn btn-secondary">📖 Ver Preguntas Frecuentes</a>
            <a href="#" class="btn btn-secondary" style="margin-left: 10px;">📚 Ver Documentación</a>
        </div>
        
        <!-- Sección Datos -->
        <div class="section">
            <h2 class="section-title">📊 Mis Datos</h2>
            <p class="section-desc">Descarga o elimina tu información</p>
            
            <button class="btn btn-secondary" onclick="alert('Descargando tus datos en formato JSON')">📥 Descargar mis datos</button>
            <button class="btn btn-danger" onclick="if(confirm('¿Deseas eliminar tu cuenta? Esta acción es irreversible.')) { alert('Cuenta eliminada'); }" style="margin-left: 10px;">🗑️ Eliminar Cuenta</button>
        </div>
        
        <!-- Información de Sistema -->
        <div class="section">
            <h2 class="section-title">ℹ️ Información del Sistema</h2>
            <p class="section-desc">Detalles técnicos de la plataforma</p>
            
            <div class="settings-group">
                <div class="settings-row">
                    <span class="settings-label">Versión de SaludWeb</span>
                    <span class="settings-value">3.0 Pro</span>
                </div>
            </div>
            
            <div class="settings-group">
                <div class="settings-row">
                    <span class="settings-label">Última actualización</span>
                    <span class="settings-value">16 de junio de 2026</span>
                </div>
            </div>
            
            <div class="settings-group">
                <div class="settings-row">
                    <span class="settings-label">Estado del servidor</span>
                    <span class="settings-value" style="color: var(--success); font-weight: 700;">✓ Operacional</span>
                </div>
            </div>
        </div>
        
    </div>

</div>
</body>
</html>
