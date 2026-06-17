<?php
// Redireccionamos al enlace original de registro usando la ruta limpia.
// Esto funciona si Apache tiene habilitado mod_rewrite y se usa el .htaccess.
header("Location: ./registro");
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Redirigiendo...</title>
	<style>body{font-family:Segoe UI,Arial,Helvetica,sans-serif;background:#f7f7f7;color:#222;padding:30px} .box{max-width:700px;margin:80px auto;background:#fff;padding:20px;border-radius:8px;border:1px solid #e6e6e6;box-shadow:0 6px 18px rgba(0,0,0,0.04)}</style>
</head>
<body>
	<div class="box">
		<h2>Redirigiendo al formulario de registro...</h2>
		<p>Si tu navegador no sigue la redirección automáticamente, podés <a href="./registro">hacer clic aquí</a>.</p>
		<hr>
		<h3>Descarga de informes e imágenes</h3>
		<p>Consultá las instrucciones para descargar informes e imágenes en <a href="info_descargas.php">Descargas: Informes/Imágenes</a>.</p>
	</div>
</body>
</html>
<?php
exit();
?>