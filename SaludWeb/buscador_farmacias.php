<?php
// SaludWEB/buscador_farmacias.php - Buscador de Farmacias
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador de Farmacias | SaludWEB</title>
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; --text: #1e293b; --success: #10b981; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 20px; margin: 0; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .back-link { text-decoration: none; color: var(--primary); font-weight: 800; font-size: 14px; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px; background: white; border: 2px solid var(--primary); transition: 0.3s; }
        .back-link:hover { background: var(--primary); color: white; }
        
        .title-section h1 { margin: 0; font-size: 28px; color: var(--text); }
        .title-section p { margin: 5px 0 0 0; color: #64748b; }
        
        .search-section { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); }
        .search-group { display: grid; grid-template-columns: 1fr 1fr auto; gap: 12px; }
        input, select { padding: 12px 14px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 14px; }
        input:focus, select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        
        .btn-search { background: var(--primary); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 14px; }
        .btn-search:hover { background: #3730a3; transform: translateY(-2px); }
        
        .farmacias-list { display: grid; gap: 16px; }
        .farmacia-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); border-left: 4px solid var(--primary); }
        
        .farmacia-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .farmacia-nombre { font-weight: 800; font-size: 18px; color: var(--text); margin: 0; }
        .farmacia-distancia { background: #e0e7ff; color: var(--primary); padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; }
        
        .farmacia-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin: 12px 0; }
        .info-item { font-size: 13px; color: #64748b; }
        .info-item strong { display: block; font-weight: 700; color: var(--text); margin-bottom: 2px; }
        
        .farmacia-horario { font-size: 13px; color: #64748b; margin: 12px 0; }
        .horario-abierto { color: var(--success); font-weight: 700; }
        .horario-cerrado { color: #ef4444; font-weight: 700; }
        
        .btn-group { display: flex; gap: 10px; margin-top: 12px; flex-wrap: wrap; }
        .btn { padding: 10px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 700; text-decoration: none; text-align: center; transition: 0.3s; font-size: 13px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #3730a3; }
        .btn-secondary { background: #f1f5f9; color: #1e293b; border: 2px solid #cbd5e1; }
        .btn-secondary:hover { background: #e2e8f0; }
        
        .empty-state { text-align: center; padding: 40px; background: white; border-radius: 12px; }
        .empty-icon { font-size: 48px; margin-bottom: 16px; }
        
        @media (max-width: 768px) {
            .search-group { grid-template-columns: 1fr; }
            .farmacia-header { flex-direction: column; }
            .farmacia-info { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="title-section">
            <h1>🏪 Buscador de Farmacias</h1>
            <p>Encuentra farmacias afiliadas cerca de ti</p>
        </div>
        <a href="mis_rx.php" class="back-link">← Volver</a>
    </div>
    
    <div class="search-section">
        <div class="search-group">
            <input type="text" placeholder="Localidad o zona..." id="searchZona">
            <select id="filtroHorario">
                <option value="todas">Todas las farmacias</option>
                <option value="abiertas">Abiertas ahora</option>
                <option value="24hs">24 Horas</option>
            </select>
            <button class="btn-search" onclick="searchFarmacias()">🔍 Buscar</button>
        </div>
    </div>
    
    <div class="farmacias-list" id="farmaciasContainer">
        <div class="farmacia-card">
            <div class="farmacia-header">
                <h3 class="farmacia-nombre">💊 Farmacia Central</h3>
                <span class="farmacia-distancia">0.5 km</span>
            </div>
            <div class="farmacia-info">
                <div class="info-item">
                    <strong>📍 Dirección</strong>
                    Av. Corrientes 1234, CABA
                </div>
                <div class="info-item">
                    <strong>📞 Teléfono</strong>
                    (011) 1234-5678
                </div>
                <div class="info-item">
                    <strong>💳 Cobertura</strong>
                    Acepta tu plan
                </div>
            </div>
            <div class="farmacia-horario">
                <span class="horario-abierto">✓ ABIERTO AHORA</span><br>
                Lun-Dom: 9:00 - 22:00
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="alert('Ruta enviada a tu navegador')">📍 Ver en Mapa</button>
                <button class="btn btn-secondary" onclick="alert('Teléfono: (011) 1234-5678')">📞 Llamar</button>
            </div>
        </div>
        
        <div class="farmacia-card">
            <div class="farmacia-header">
                <h3 class="farmacia-nombre">💊 Farmafarm Express</h3>
                <span class="farmacia-distancia">1.2 km</span>
            </div>
            <div class="farmacia-info">
                <div class="info-item">
                    <strong>📍 Dirección</strong>
                    Calle Florida 567, CABA
                </div>
                <div class="info-item">
                    <strong>📞 Teléfono</strong>
                    (011) 2345-6789
                </div>
                <div class="info-item">
                    <strong>💳 Cobertura</strong>
                    Acepta tu plan
                </div>
            </div>
            <div class="farmacia-horario">
                <span class="horario-abierto">✓ ABIERTO AHORA</span><br>
                Lun-Dom: 8:00 - 23:00
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="alert('Ruta enviada a tu navegador')">📍 Ver en Mapa</button>
                <button class="btn btn-secondary" onclick="alert('Teléfono: (011) 2345-6789')">📞 Llamar</button>
            </div>
        </div>
        
        <div class="farmacia-card">
            <div class="farmacia-header">
                <h3 class="farmacia-nombre">💊 Farmacia del Dr</h3>
                <span class="farmacia-distancia">2.1 km</span>
            </div>
            <div class="farmacia-info">
                <div class="info-item">
                    <strong>📍 Dirección</strong>
                    Av. 9 de Julio 890, CABA
                </div>
                <div class="info-item">
                    <strong>📞 Teléfono</strong>
                    (011) 3456-7890
                </div>
                <div class="info-item">
                    <strong>💳 Cobertura</strong>
                    Acepta tu plan
                </div>
            </div>
            <div class="farmacia-horario">
                <span style="color: #f59e0b; font-weight: 700;">⏰ CIERRA A LAS 20:00</span><br>
                Lun-Sáb: 9:00 - 20:00 | Dom: Cerrado
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="alert('Ruta enviada a tu navegador')">📍 Ver en Mapa</button>
                <button class="btn btn-secondary" onclick="alert('Teléfono: (011) 3456-7890')">📞 Llamar</button>
            </div>
        </div>
    </div>
    
    <script>
        function searchFarmacias() {
            const zona = document.getElementById('searchZona').value;
            const horario = document.getElementById('filtroHorario').value;
            console.log('Buscando farmacias en:', zona, 'Filtro:', horario);
            // Aquí iría la lógica de búsqueda
        }
    </script>
</div>
</body>
</html>
