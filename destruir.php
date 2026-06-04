<?php
/**
 * destruir.php
 * Elimina todas las cookies creadas y destruye la sesión actual.
 * Luego redirige al formulario principal (index.php).
 */
session_start();    // Debe iniciarse antes de poder destruirla

/* ══════════════════════════════════════════
   1. DESTRUIR LA SESIÓN
   ══════════════════════════════════════════ */

session_unset();    // Elimina todas las variables de la sesión ($_SESSION = [])
session_destroy();  // Destruye los datos de sesión en el servidor

/* ══════════════════════════════════════════
   2. ELIMINAR COOKIES
   Se sobreescribe cada cookie con fecha de expiración en el pasado
   para que el navegador la borre de inmediato.
   ══════════════════════════════════════════ */

// Cookie 1: nombre del estudiante
setcookie('nombre_estudiante', '', time() - 3600, '/');

// Cookie 2: correo electrónico
setcookie('correo_estudiante', '', time() - 3600, '/');

// Cookie 3: teléfono del estudiante
setcookie('telefono_estudiante', '', time() - 3600, '/');

/* ══════════════════════════════════════════
   3. REDIRECCIÓN AL FORMULARIO PRINCIPAL
   ══════════════════════════════════════════ */

header('Location: index.php');
exit;