<<<<<<< HEAD
# T5 – Cookies & Sesiones · Inscripción de Cursos Técnicos

## Estructura del proyecto

```
t5Cookies/
├── index.php          ← Formulario principal
├── procesar.php       ← Procesamiento, cookies, sesiones y factura
├── destruir.php       ← Elimina cookies y sesión, redirige a index
├── CSS/
│   └── styles.css     ← Todos los estilos (sin frameworks externos)
├── HTML/
│   ├── nav.html       ← Encabezado / navegación
│   ├── main.html      ← Formulario de inscripción (incluido por index.php)
│   └── footer.html    ← Pie de página
└── includes/
    └── cursos.php     ← Array $cursos con precios por módulo
```

## Requisitos

- XAMPP (Apache + PHP 8+)
- Sin base de datos

## Instalación

1. Copia la carpeta `t5Cookies/` en `C:\xampp\htdocs\`
2. Inicia Apache desde el Panel de Control de XAMPP
3. Abre `http://localhost/t5Cookies/`

## Flujo de la aplicación

1. `index.php` → muestra el formulario
2. Al enviar → `procesar.php` valida, guarda cookies y sesión, muestra factura
3. Botón "Cerrar Sesión y Borrar Cookies" → `destruir.php` limpia todo y redirige
=======
# t5Cookies
>>>>>>> d7ba088c367f64d6645df90d6b3fcc802162f7d0
