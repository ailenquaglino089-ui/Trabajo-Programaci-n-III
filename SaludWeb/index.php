<?php
// Corregimos la redirección: al estar ya dentro de SaludWeb, 
// debemos ir directamente al Dashboard para evitar un bucle 404.
header("Location: ./lista");
exit();
?>