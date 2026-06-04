<?php
// Redireccionamos al enlace original de registro usando la ruta limpia.
// Esto funciona si Apache tiene habilitado mod_rewrite y se usa el .htaccess.
header("Location: ./registro");
exit();
?>