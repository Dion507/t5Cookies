<?php
/**
 * procesar.php - Lógica de Negocio y Control de Arrays
 * * Se encarga del procesamiento algorítmico sin mezclar interfaces HTML directas.
 * Al terminar los ciclos e iteraciones, incluye la vista del resultado.
 */

session_start();
require_once './includes/cursos.php'; 

// Función auxiliar para limpieza de datos
function limpiar(string $valor): string {
    return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
}

$errores = [];

/* ══════════════════════════════════════════
   1. RECEPCIÓN Y LLENADO DE ARRAYS CON CICLOS
   ══════════════════════════════════════════ */
$estudiante = [
    "nombre"   => "",
    "correo"   => "",
    "telefono" => ""
];
$clavesEstudiante = array_keys($estudiante);

for ($i = 0; $i < count($clavesEstudiante); $i++) {
    $clave = $clavesEstudiante[$i];
    if (isset($_POST[$clave])) {
        $estudiante[$clave] = limpiar($_POST[$clave]);
    }
}

$inscripcion = [
    "curso"     => "",
    "modulos"   => 0,
    "turno"     => "",
    "modalidad" => ""
];
$clavesInscripcion = array_keys($inscripcion);

for ($i = 0; $i < count($clavesInscripcion); $i++) {
    $clave = $clavesInscripcion[$i];
    if (isset($_POST[$clave])) {
        if ($clave === 'modulos') {
            $inscripcion[$clave] = (int)$_POST[$clave];
        } else {
            $inscripcion[$clave] = limpiar($_POST[$clave]);
        }
    }
}

/* ══════════════════════════════════════════
   2. VALIDACIONES
   ══════════════════════════════════════════ */
if ($estudiante['nombre'] === '') $errores[] = 'El nombre es obligatorio.';
if (!filter_var($estudiante['correo'], FILTER_VALIDATE_EMAIL)) $errores[] = 'Correo electrónico inválido.';
if ($inscripcion['modulos'] < 1 || $inscripcion['modulos'] > 12) $errores[] = 'La cantidad de módulos debe estar entre 1 y 12.';

$cursoValido = false;
$nombreCurso = $inscripcion['curso'];

foreach ($cursos as $nombreDelCursoDisponible => $precio) {
    if ($nombreDelCursoDisponible === $nombreCurso) {
        $cursoValido = true;
        break;
    }
}
if (!$cursoValido) $errores[] = 'Selecciona un curso válido de la lista.';

if (!empty($errores)) {
    echo "<h2>⚠️ Errores en el formulario</h2><ul>";
    foreach ($errores as $e) { echo "<li>$e</li>"; }
    echo "</ul><br><a href='index.php'>← Volver al formulario</a>";
    exit;
}

/* ══════════════════════════════════════════
   3. PROCESAMIENTO Y CÁLCULOS UTILIZANDO ARRAYS
   ══════════════════════════════════════════ */
$precio_modulo = 0;
foreach ($cursos as $nombreDelCurso => $precio) {
    if ($nombreDelCurso === $inscripcion['curso']) {
        $precio_modulo = $precio;
        break; 
    }
}

$subtotal_raw  = $precio_modulo * $inscripcion['modulos'];
$descuento_raw = ($inscripcion['modulos'] > 3) ? ($subtotal_raw * 0.20) : 0;
$itbms_raw     = $subtotal_raw * 0.07;
$total_raw     = $subtotal_raw - $descuento_raw + $itbms_raw;

$factura = [
    "precio_modulo" => $precio_modulo,
    "subtotal"      => $subtotal_raw,
    "descuento"     => $descuento_raw,
    "itbms"         => $itbms_raw,
    "total"         => $total_raw
];

/* ══════════════════════════════════════════
   4. PERSISTENCIA: COOKIES Y SESIONES PASO A PASO
   ══════════════════════════════════════════ */
$datosCookie = [
    'nombre_estudiante' => $estudiante['nombre'],
    'curso_favorito'    => $inscripcion['curso']
];

$clavesCookies = array_keys($datosCookie);
for ($i = 0; $i < count($clavesCookies); $i++) {
    $nombreCookie = $clavesCookies[$i];
    $valorCookie  = $datosCookie[$nombreCookie];
    setcookie($nombreCookie, $valorCookie, time() + 3600, '/');
}

$_SESSION["datos"] = [
    "estudiante"  => [],
    "inscripcion" => [],
    "factura"     => []
];

foreach ($estudiante as $clave => $valor) {
    $_SESSION["datos"]["estudiante"][$clave] = $valor;
}
foreach ($inscripcion as $clave => $valor) {
    $_SESSION["datos"]["inscripcion"][$clave] = $valor;
}
foreach ($factura as $clave => $valor) {
    $_SESSION["datos"]["factura"][$clave] = number_format($valor, 2);
}

// ══════════════════════════════════════════
// 5. INCLUSIÓN DE LA VISTA DE RESULTADO
// ══════════════════════════════════════════
include './HTML/resultado.html';