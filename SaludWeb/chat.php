<?php
// SaludWEB/chat.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente Inteligente | SaludWEB</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #eef2f6; margin: 0; padding: 0; }
        .container { max-width: 900px; margin: 0 auto; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .header a { color: #fff; background: #007bff; text-decoration: none; padding: 10px 18px; border-radius: 10px; }
        .card { background: #fff; border-radius: 18px; box-shadow: 0 18px 35px rgba(0,0,0,0.08); padding: 25px; }
        .chat-area { min-height: 420px; border: 1px solid #d9e2ec; border-radius: 16px; padding: 18px; overflow-y: auto; background: #f8fbff; }
        .message { margin-bottom: 18px; display: flex; gap: 14px; }
        .message.user { justify-content: flex-end; }
        .bubble { max-width: 75%; padding: 14px 18px; border-radius: 20px; line-height: 1.6; white-space: pre-wrap; }
        .bubble.user { background: #007bff; color: #fff; border-bottom-right-radius: 4px; }
        .bubble.assistant { background: #f1f5f9; color: #111827; border-bottom-left-radius: 4px; }
        .input-row { display: grid; grid-template-columns: 1fr auto; gap: 12px; margin-top: 20px; }
        textarea { width: 100%; min-height: 100px; resize: vertical; border-radius: 14px; border: 1px solid #d1d5db; padding: 16px; font-size: 1rem; }
        button { border: none; background: #007bff; color: #fff; font-weight: bold; padding: 0 24px; border-radius: 14px; cursor: pointer; }
        button:disabled { background: #6c8bff; cursor: not-allowed; }
        .note { margin-top: 14px; color: #475569; font-size: 0.95rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Asistente Inteligente</h1>
                <p>Haz preguntas sobre el proyecto, las rutas API o cómo usar SaludWEB.</p>
            </div>
            <a href="lista">← Volver al dashboard</a>
        </div>

        <div class="card">
            <div class="chat-area" id="chatArea"></div>

            <div class="input-row">
                <textarea id="chatInput" placeholder="Escribe tu pregunta aquí..." aria-label="Mensaje al asistente"></textarea>
                <button id="sendButton">Enviar</button>
            </div>
            <p class="note">Si quieres una respuesta más potente, configura <strong>OPENAI_API_KEY</strong> en el servidor o en el archivo <code>config/openai.php</code>.</p>
        </div>
    </div>

    <script>
        const chatArea = document.getElementById('chatArea');
        const chatInput = document.getElementById('chatInput');
        const sendButton = document.getElementById('sendButton');
        const apiUrl = 'api/chat';

        function addMessage(text, sender) {
            const wrapper = document.createElement('div');
            wrapper.className = 'message ' + sender;

            const bubble = document.createElement('div');
            bubble.className = 'bubble ' + sender;
            bubble.textContent = text;

            wrapper.appendChild(bubble);
            chatArea.appendChild(wrapper);
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        async function sendMessage() {
            const message = chatInput.value.trim();
            if (!message) return;
            addMessage(message, 'user');
            chatInput.value = '';
            sendButton.disabled = true;
            addMessage('Escribiendo respuesta...', 'assistant');

            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message })
                });

                const assistantBubbles = document.querySelectorAll('.message.assistant .bubble');
                const lastBubble = assistantBubbles[assistantBubbles.length - 1];

                if (!response.ok) {
                    const text = await response.text();
                    lastBubble.textContent = 'Error del servidor: ' + response.status + ' - ' + text;
                } else {
                    const result = await response.json();
                    if (result.data && result.data.answer) {
                        lastBubble.textContent = result.data.answer;
                    } else if (result.error) {
                        lastBubble.textContent = 'Error: ' + result.error;
                    } else {
                        lastBubble.textContent = 'No se recibió respuesta del asistente.';
                    }
                }
            } catch (error) {
                const assistantBubbles = document.querySelectorAll('.message.assistant .bubble');
                const lastBubble = assistantBubbles[assistantBubbles.length - 1];
                lastBubble.textContent = 'Error de conexión: ' + error.message;
            } finally {
                sendButton.disabled = false;
                chatInput.focus();
            }
        }

        sendButton.addEventListener('click', sendMessage);
        chatInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        });
    </script>
</body>
</html>
