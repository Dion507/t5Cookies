<<<<<<< HEAD
<?php
/**
 * index.php
 * Página principal: muestra el formulario de inscripción.
 * Inicia sesión para detectar si ya hay datos previos.
 */
session_start();

// Incluye el array asociativo de cursos (fuente única de verdad)
require_once './includes/cursos.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción de Cursos Técnicos</title>
    <link rel="stylesheet" href="./CSS/styles.css">
</head>
<body>

<nav>
    <?php include './HTML/nav.html'; ?>
</nav>

<main>
    <?php include './HTML/main.html'; ?>
</main>

<footer>
    <?php include './HTML/footer.html'; ?>
</footer>

</body>
</html>
=======
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>T5P3</title>
    <link rel="stylesheet" href="./CSS/styles.css">
</head>
<nav>
    <?php include "./HTML/nav.html" ?>
</nav>
<main>
    <?php include "./HTML/main.html" ?>
</main>
<footer>
    <?php include "./HTML/footer.html" ?>
</footer>

</html>
>>>>>>> d7ba088c367f64d6645df90d6b3fcc802162f7d0
