<?php
// SaludWEB/prestaciones.php - Consulta de Prestaciones
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestaciones | SaludWEB</title>
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; --text: #1e293b; --success: #10b981; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 20px; margin: 0; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .back-link { text-decoration: none; color: var(--primary); font-weight: 800; font-size: 14px; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px; background: white; border: 2px solid var(--primary); transition: 0.3s; }
        .back-link:hover { background: var(--primary); color: white; }
        
        .title-section h1 { margin: 0; font-size: 28px; color: var(--text); }
        .title-section p { margin: 5px 0 0 0; color: #64748b; }
        
        .search-box { margin-bottom: 24px; }
        .search-box input { width: 100%; padding: 14px 18px; border: 2px solid #cbd5e1; border-radius: 10px; font-size: 14px; }
        .search-box input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        
        .prestaciones-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        
        .prestacion-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); border: 2px solid #e2e8f0; transition: 0.3s; }
        .prestacion-card:hover { transform: translateY(-4px); box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); border-color: var(--primary); }
        
        .prestacion-icon { font-size: 32px; margin-bottom: 12px; }
        .prestacion-nombre { font-weight: 800; font-size: 16px; margin: 0 0 8px 0; color: var(--text); }
        .prestacion-desc { font-size: 13px; color: #64748b; margin: 0 0 12px 0; line-height: 1.5; }
        .prestacion-status { display: inline-block; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; margin-bottom: 12px; }
        .status-activa { background: #dcfce7; color: var(--success); }
        .status-limitada { background: #fef3c7; color: #b45309; }
        
        .btn { display: inline-block; padding: 10px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 700; text-decoration: none; text-align: center; transition: 0.3s; font-size: 13px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #3730a3; transform: translateY(-2px); }
        
        .categories { display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap; }
        .category-btn { padding: 8px 14px; border-radius: 8px; border: 2px solid #cbd5e1; background: white; color: var(--text); cursor: pointer; font-weight: 700; transition: 0.3s; font-size: 13px; }
        .category-btn.active { background: var(--primary); color: white; border-color: var(--primary); }
        .category-btn:hover { border-color: var(--primary); }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="title-section">
            <h1>🏥 Prestaciones</h1>
            <p>Consulta servicios disponibles en tu cobertura</p>
        </div>
        <a href="mis_rx.php" class="back-link">← Volver</a>
    </div>
    
    <div class="search-box">
        <input type="text" placeholder="🔍 Buscar prestaciones..." id="searchPrestaciones">
    </div>
    
    <div class="categories">
        <button class="category-btn active" onclick="filterCategory('todas')">Todas</button>
        <button class="category-btn" onclick="filterCategory('medico')">Consultas Médicas</button>
        <button class="category-btn" onclick="filterCategory('estudios')">Estudios</button>
        <button class="category-btn" onclick="filterCategory('dental')">Odontología</button>
        <button class="category-btn" onclick="filterCategory('farmacia')">Farmacia</button>
    </div>
    
    <div class="prestaciones-grid">
        <div class="prestacion-card" data-category="medico">
            <div class="prestacion-icon">👨‍⚕️</div>
            <h3 class="prestacion-nombre">Consulta Médica General</h3>
            <p class="prestacion-desc">Atención de médicos clínicos para consultas de salud general.</p>
            <span class="prestacion-status status-activa">ACTIVA</span>
            <button class="btn btn-primary">Ver Detalles</button>
        </div>
        
        <div class="prestacion-card" data-category="medico">
            <div class="prestacion-icon">🔬</div>
            <h3 class="prestacion-nombre">Consulta Especialista</h3>
            <p class="prestacion-desc">Atención de especialistas en diversas áreas médicas.</p>
            <span class="prestacion-status status-activa">ACTIVA</span>
            <button class="btn btn-primary">Ver Detalles</button>
        </div>
        
        <div class="prestacion-card" data-category="estudios">
            <div class="prestacion-icon">🔬</div>
            <h3 class="prestacion-nombre">Análisis de Laboratorio</h3>
            <p class="prestacion-desc">Exámenes de sangre y laboratorio en red de centros autorizados.</p>
            <span class="prestacion-status status-activa">ACTIVA</span>
            <button class="btn btn-primary">Ver Detalles</button>
        </div>
        
        <div class="prestacion-card" data-category="estudios">
            <div class="prestacion-icon">📸</div>
            <h3 class="prestacion-nombre">Radiografías y Tomografías</h3>
            <p class="prestacion-desc">Estudios de imagen en centros diagnósticos acreditados.</p>
            <span class="prestacion-status status-limitada">LIMITADA</span>
            <button class="btn btn-primary">Ver Detalles</button>
        </div>
        
        <div class="prestacion-card" data-category="dental">
            <div class="prestacion-icon">🦷</div>
            <h3 class="prestacion-nombre">Odontología Preventiva</h3>
            <p class="prestacion-desc">Limpiezas, profilaxis y control de caries.</p>
            <span class="prestacion-status status-activa">ACTIVA</span>
            <button class="btn btn-primary">Ver Detalles</button>
        </div>
        
        <div class="prestacion-card" data-category="farmacia">
            <div class="prestacion-icon">💊</div>
            <h3 class="prestacion-nombre">Medicamentos Recetados</h3>
            <p class="prestacion-desc">Cobertura de medicamentos con prescripción médica.</p>
            <span class="prestacion-status status-activa">ACTIVA</span>
            <button class="btn btn-primary">Ver Detalles</button>
        </div>
        
        <div class="prestacion-card" data-category="farmacia">
            <div class="prestacion-icon">🏪</div>
            <h3 class="prestacion-nombre">Medicamentos sin Receta</h3>
            <p class="prestacion-desc">Cobertura limitada en medicamentos de venta libre.</p>
            <span class="prestacion-status status-limitada">LIMITADA</span>
            <button class="btn btn-primary">Ver Detalles</button>
        </div>
    </div>
    
    <script>
        function filterCategory(category) {
            document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            const cards = document.querySelectorAll('.prestacion-card');
            cards.forEach(card => {
                if (category === 'todas' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        document.getElementById('searchPrestaciones').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.prestacion-card').forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
    </script>
</div>
</body>
</html>
