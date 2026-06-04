<?php
/**
 * procesar.php - Lógica de Negocio y Control de Arrays
 * Se encarga del procesamiento algorítmico sin mezclar interfaces HTML directas.
 * Al terminar los ciclos e iteraciones, incluye la vista del resultado.
 */

session_start();
require_once './includes/cursos.php'; 

// Función auxiliar para limpieza de datos
function limpiar(string $valor): string {
    return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
}

try {
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
       2. VALIDACIONES ESTRICTAS (REPLICANDO EL HTML)
       ══════════════════════════════════════════ */
    
    // Validación de Nombre: Solo letras y espacios (Nombre Apellido). Sin números ni símbolos.
    // Expresión equivalente al pattern HTML: ^[A-Za-záéíóúÁÉÍÓÚñÑ]+(\s[A-Za-záéíóúÁÉÍÓÚñÑ]+)+$
    if ($estudiante['nombre'] === '') {
        throw new Exception('El nombre es obligatorio.');
    }
    if (!preg_match('/^[A-Za-záéíóúÁÉÍÓÚñÑ]+(\s[A-Za-záéíóúÁÉÍÓÚñÑ]+)+$/', $estudiante['nombre'])) {
        throw new Exception('Formato de nombre inválido. Debe ingresar Nombre y Apellido (solo letras y espacios).');
    }

    // Validación de Correo: Soporta cualquier dominio y estructura estándar @
    if (!filter_var($estudiante['correo'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El correo electrónico no tiene un formato válido.');
    }

    // Validación de Teléfono: Formato estricto numérico ####-####
    if (!preg_match('/^[0-9]{4}-[0-9]{4}$/', $estudiante['telefono'])) {
        throw new Exception('El formato de teléfono debe ser estrictamente de 8 números con un guion (Ej. 6000-0000).');
    }

    // Validación de Módulos
    if ($inscripcion['modulos'] < 1 || $inscripcion['modulos'] > 12) {
        throw new Exception('La cantidad de módulos debe estar entre 1 y 12.');
    }

    // Validación de Curso Existente
    $cursoValido = false;
    $nombreCurso = $inscripcion['curso'];

    foreach ($cursos as $nombreDelCursoDisponible => $precio) {
        if ($nombreDelCursoDisponible === $nombreCurso) {
            $cursoValido = true;
            break;
        }
    }
    if (!$cursoValido) {
        throw new Exception('Selecciona un curso válido de la lista.');
    }

    // Validación de campos requeridos adicionales (Radio Buttons)
    if (empty($inscripcion['turno'])) {
        throw new Exception('Debe seleccionar un turno (Diurno o Nocturno).');
    }
    if (empty($inscripcion['modalidad'])) {
        throw new Exception('Debe seleccionar una modalidad (Virtual o Presencial).');
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
    'nombre_estudiante'   => $estudiante['nombre'],
    'correo_estudiante'   => $estudiante['correo'],
    'telefono_estudiante' => $estudiante['telefono']
];

$clavesCookies = array_keys($datosCookie);
for ($i = 0; $i < count($clavesCookies); $i++) {
    $nombreCookie = $clavesCookies[$i];
    $valorCookie  = $datosCookie[$nombreCookie];
    
    // Guarda dinámicamente solo las 3 cookies personales en el navegador por 1 hora
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

    // 5. INCLUSIÓN DE LA VISTA DE RESULTADO (ÉXITO)
    include './HTML/resultado.html';

} catch (Exception $e) {
    /* ══════════════════════════════════════════
       MANEJO DE ERRORES CON DISEÑO ELEGANTE
       ══════════════════════════════════════════ */
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Error en la Solicitud</title>
        <link rel="stylesheet" href="./CSS/styles.css">
    </head>
    <body>
        <main style="margin-top: 3rem;">
            <div class="alerta">
                <strong>⚠️ Error de Validación:</strong> <?= htmlspecialchars($e->getMessage()) ?>
            </div>
            <div class="btn-row">
                <a href="index.php" class="btn btn-secondary" style="text-align: center; width: 100%;">← Volver al Formulario</a>
            </div>
        </main>
    </body>
    </html>
    <?php
    exit;
}