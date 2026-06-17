<?php
// redir_externo.php
// Comprueba desde el servidor si un dominio responde y redirige al primero disponible.

function domain_responds($url, $timeout = 5) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errno = curl_errno($ch);
    curl_close($ch);
    if ($errno !== 0) return false;
    return ($code >= 200 && $code < 400);
}

$d = $_GET['d'] ?? '';
$d = trim($d);
if ($d === '') {
    echo "Parámetros inválidos.";
    exit();
}

// Normalizar posibles entradas
if (!preg_match('#^https?://#i', $d)) {
    $d = 'https://' . $d;
}

$alt = null;
// Si es .com probamos .com.ar como alternativa y viceversa
if (strpos($d, '.com.ar') !== false) {
    $alt = str_replace('.com.ar', '.com', $d);
} elseif (strpos($d, '.com') !== false) {
    $alt = str_replace('.com', '.com.ar', $d);
}

// Intentamos el dominio principal
if (domain_responds($d, 5)) {
    header('Location: ' . $d);
    exit();
}

// Si hay alternativa y responde, redirigimos
if ($alt && domain_responds($alt, 5)) {
    header('Location: ' . $alt);
    exit();
}

// Si ninguno responde, mostramos una página que ofrece ambos enlaces directos
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Redirección externa</title>
  <style>body{font-family:Segoe UI,Arial,Helvetica,sans-serif;background:#f7f7f7;color:#222;padding:20px}.box{max-width:760px;margin:40px auto;background:#fff;padding:20px;border-radius:8px;border:1px solid #e6e6e6}</style>
</head>
<body>
  <div class="box">
    <h2>No se pudo abrir automáticamente el portal externo</h2>
    <p>Intenté abrir: <strong><?php echo htmlspecialchars($d); ?></strong></p>
    <?php if ($alt): ?>
      <p>Alternativa: <strong><?php echo htmlspecialchars($alt); ?></strong></p>
    <?php endif; ?>
    <p>Puedes intentar abrir manualmente uno de los enlaces siguientes:</p>
    <ul>
      <li><a href="<?php echo htmlspecialchars($d); ?>" target="_blank" rel="noopener">Abrir <?php echo htmlspecialchars($d); ?></a></li>
      <?php if ($alt): ?>
      <li><a href="<?php echo htmlspecialchars($alt); ?>" target="_blank" rel="noopener">Abrir <?php echo htmlspecialchars($alt); ?></a></li>
      <?php endif; ?>
    </ul>
    <p>Si aún no podés acceder, probá desde otra red o consultá con el administrador de la clínica.</p>
  </div>
</body>
</html>
