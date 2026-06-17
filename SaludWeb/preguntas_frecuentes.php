<?php
// SaludWEB/preguntas_frecuentes.php - Preguntas Frecuentes
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntas Frecuentes | SaludWEB</title>
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; --text: #1e293b; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 20px; margin: 0; }
        .container { max-width: 800px; margin: 0 auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .back-link { text-decoration: none; color: var(--primary); font-weight: 800; font-size: 14px; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px; background: white; border: 2px solid var(--primary); transition: 0.3s; }
        .back-link:hover { background: var(--primary); color: white; }
        
        .title-section h1 { margin: 0; font-size: 28px; color: var(--text); }
        .title-section p { margin: 5px 0 0 0; color: #64748b; }
        
        .faq-item { background: white; border-radius: 12px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); overflow: hidden; }
        .faq-question { padding: 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 800; color: var(--text); transition: 0.3s; background: white; }
        .faq-question:hover { background: #f8fafc; }
        .faq-question.active { background: var(--primary); color: white; }
        
        .faq-toggle { font-size: 18px; transition: transform 0.3s; }
        .faq-question.active .faq-toggle { transform: rotate(180deg); }
        
        .faq-answer { padding: 0 20px; max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; color: #64748b; }
        .faq-answer.show { max-height: 500px; padding: 20px; }
        .faq-answer p { margin: 0; line-height: 1.6; }
        
        .search-box { margin-bottom: 24px; }
        .search-box input { width: 100%; padding: 14px 18px; border: 2px solid #cbd5e1; border-radius: 10px; font-size: 14px; }
        .search-box input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="title-section">
            <h1>❓ Preguntas Frecuentes</h1>
            <p>Encuentra respuestas a tus dudas</p>
        </div>
        <a href="mis_rx.php" class="back-link">← Volver</a>
    </div>
    
    <div class="search-box">
        <input type="text" placeholder="🔍 Buscar pregunta..." id="searchFAQ" onkeyup="filterFAQ()">
    </div>
    
    <div id="faqContainer">
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>¿Cómo puedo renovar mis prescripciones?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Para renovar tus prescripciones, puedes ir a la sección "Mis Rx" > "Prescripciones" y buscar las que deseas renovar. Si están disponibles para renovación, verás un botón "Renovar". También puedes contactar directamente a tu médico.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>¿Cuáles son las farmacias afiliadas en mi zona?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Utiliza el "Buscador de Farmacias" en la sección "Mis Rx". Ingresa tu zona o localidad y podrás ver todas las farmacias afiliadas cercanas, junto con sus horarios y distancia desde tu ubicación.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>¿Cómo sé si una prestación está cubierta por mi plan?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>En la sección "Prestaciones" encontrarás un listado completo de todos los servicios cubiertos por tu plan. Cada prestación muestra si está activa, limitada o no incluida. Para consultas específicas sobre tu cobertura, contacta a nuestro equipo de soporte.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>¿Cuáles son los horarios de atención del soporte?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Nuestro equipo de soporte está disponible de lunes a viernes de 8:00 a 20:00 hs. Puedes contactarnos por WhatsApp, email o a través del formulario de contacto. Los fines de semana contamos con atención limitada para casos urgentes.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>¿Qué documentos necesito para recibir una prestación?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Generalmente necesitarás tu carnet de afiliado/carnet de obra social y tu DNI. Para prestaciones específicas como estudios o consultas especializadas, el médico tratante puede solicitar documentación adicional. Siempre confirma antes con el prestador.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>¿Cómo puedo descargar mis informes médicos?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Todos tus informes médicos están disponibles en tu cuenta. Ve a "Mis Rx" > "Prescripciones" o accede directamente a la sección de descargas en el menú principal. Los informes se descargan en formato PDF seguro.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span>¿Es seguro usar esta plataforma?</span>
                <span class="faq-toggle">▼</span>
            </div>
            <div class="faq-answer">
                <p>Sí, utilizamos encriptación de nivel industrial (HTTPS) y cumplimos con todas las normativas de protección de datos personales (RGPD, LGPD). Tu información médica está protegida y solo accesible por ti y profesionales autorizados.</p>
            </div>
        </div>
    </div>
    
    <script>
        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            const question = element;
            
            // Cerrar todas las otras respuestas
            document.querySelectorAll('.faq-answer').forEach(a => {
                if (a !== answer) {
                    a.classList.remove('show');
                    a.previousElementSibling.classList.remove('active');
                }
            });
            
            // Toggle la actual
            answer.classList.toggle('show');
            question.classList.toggle('active');
        }
        
        function filterFAQ() {
            const searchTerm = document.getElementById('searchFAQ').value.toLowerCase();
            document.querySelectorAll('.faq-item').forEach(item => {
                const question = item.querySelector('.faq-question').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
</div>
</body>
</html>
