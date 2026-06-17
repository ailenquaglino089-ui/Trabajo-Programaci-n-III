<?php
// SaludWEB/cuenta.php - Página de Gestión de Cuenta (Complementaria a Configuración)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta | SaludWEB</title>
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; --text: #1e293b; --success: #10b981; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 20px; margin: 0; }
        .container { max-width: 800px; margin: 0 auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .back-link { text-decoration: none; color: var(--primary); font-weight: 800; font-size: 14px; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px; background: white; border: 2px solid var(--primary); transition: 0.3s; }
        .back-link:hover { background: var(--primary); color: white; }
        
        .title-section h1 { margin: 0; font-size: 28px; color: var(--text); }
        .title-section p { margin: 5px 0 0 0; color: #64748b; }
        
        .card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); margin-bottom: 20px; }
        .profile-header { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #e2e8f0; }
        .profile-avatar { width: 80px; height: 80px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: 800; }
        .profile-info h2 { margin: 0 0 5px 0; font-size: 20px; }
        .profile-info p { margin: 0; color: #64748b; font-size: 14px; }
        
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-weight: 700; margin-bottom: 6px; color: var(--text); }
        .form-group input { width: 100%; padding: 12px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 14px; }
        .form-group input:focus { outline: none; border-color: var(--primary); }
        
        .btn { display: inline-block; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 800; text-decoration: none; text-align: center; transition: 0.3s; font-size: 14px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #3730a3; }
        
        .info-section { background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid var(--primary); }
        .info-section p { margin: 0; color: #64748b; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="title-section">
            <h1>👤 Mi Cuenta</h1>
            <p>Gestiona tu perfil y datos personales</p>
        </div>
        <a href="configuracion.php" class="back-link">← Volver</a>
    </div>
    
    <div class="card">
        <div class="profile-header">
            <div class="profile-avatar">👤</div>
            <div class="profile-info">
                <h2>Juan Pérez</h2>
                <p>usuario@saludweb.com</p>
                <p>Miembro desde: 15 de enero de 2024</p>
            </div>
        </div>
        
        <div class="form-group">
            <label>Nombre completo</label>
            <input type="text" value="Juan Carlos Pérez" readonly>
        </div>
        
        <div class="form-group">
            <label>Correo electrónico</label>
            <input type="email" value="usuario@saludweb.com" readonly>
        </div>
        
        <div class="form-group">
            <label>Teléfono</label>
            <input type="tel" value="+54 9 11 2345-6789" readonly>
        </div>
        
        <div class="form-group">
            <label>Fecha de nacimiento</label>
            <input type="date" value="1985-06-15" readonly>
        </div>
        
        <button class="btn btn-primary" onclick="alert('Función de editar perfil')">✏️ Editar Perfil</button>
    </div>
    
    <div class="card">
        <h3 style="margin-top: 0;">🔐 Seguridad</h3>
        
        <div class="info-section">
            <p><strong>Contraseña:</strong> Última cambio hace 180 días</p>
        </div>
        
        <button class="btn btn-primary" onclick="alert('Redirigiendo a cambio de contraseña')" style="margin-bottom: 10px;">🔐 Cambiar Contraseña</button>
        
        <h4 style="margin-top: 20px; margin-bottom: 10px;">Autenticación de dos factores</h4>
        <div class="info-section">
            <p>Aumenta la seguridad de tu cuenta activando la autenticación de dos factores.</p>
        </div>
        <button class="btn btn-primary" onclick="alert('Función de 2FA')">🔒 Activar 2FA</button>
    </div>
    
    <div class="card">
        <h3 style="margin-top: 0;">🗑️ Zona de Peligro</h3>
        
        <div class="info-section">
            <p style="color: #dc2626; font-weight: 700;">⚠️ Eliminar cuenta es una acción irreversible. Se borrarán todos tus datos.</p>
        </div>
        
        <button class="btn" style="background: #ef4444; color: white;" onclick="if(confirm('¿Deseas eliminar tu cuenta y todos tus datos? Esta acción es irreversible.')) { alert('Cuenta eliminada'); }">🗑️ Eliminar Cuenta Permanentemente</button>
    </div>
</div>
</body>
</html>
