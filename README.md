# CWU - Celebrate With Us

Aplicación web para gestión de reservas de eventos y escape rooms.

## Requisitos

- [XAMPP](https://www.apachefriends.org/) (PHP + MySQL)
- [Node.js](https://nodejs.org/) v22 LTS o superior

## Instalación

```bash
# 1. Clona el repositorio en htdocs de XAMPP
git clone <url-repo> c:/xampp/htdocs/CWU

# 2. Instala las dependencias
npm install
```

## Puesta en marcha

### 1. Inicia XAMPP
- Abre el **Panel de control de XAMPP**
- Arranca **Apache** y **MySQL**
- Importa la base de datos: abre `http://localhost/phpmyadmin` y ejecuta `database/script-completo.sql`

### 2. Inicia Vite (desarrollo)
```bash
npm run dev
```

### 3. Abre el proyecto
```
http://localhost/cwu/
```

> Mientras `npm run dev` esté corriendo, los cambios en `src/` se reflejan en el navegador automáticamente.

## Producción

```bash
# Genera los assets optimizados en public/build/
npm run build
```

Una vez ejecutado, ya no necesitas tener `npm run dev` corriendo.

## Crear una vista nueva

1. Crear el archivo PHP en `Vistas/[modulo]/NombreView.php`
2. Si necesita JS/CSS propio, crear `src/[modulo]/nombre.js` (e importar el CSS desde ahí)
3. Si el módulo no tiene entry en Vite aún, añadirlo en `vite.config.js`
4. En el `<head>` del PHP incluir: `<?php include '../../inc/vite.php'; vite_assets('modulo/nombre'); ?>`

## Añadir una librería nueva

```bash
# 1. Instalar con npm
npm install nombre-libreria

# 2. Importarla en src/main.js (si es global) o en el JS del módulo
import 'nombre-libreria/dist/archivo.css'
import 'nombre-libreria'
```

## Importante — scripts type="module"

El script de Vite es `type="module"` (asíncrono). Los inline scripts en los PHP
se ejecutan antes de que el módulo cargue, por lo que **no pueden llamar a funciones
de las librerías directamente**.

```html
<!-- ❌ No hacer -->
<script>
    flatpickr("#cal", { ... }) // Error: flatpickr is not defined
</script>

<!-- ✅ Correcto — inicializar en src/[modulo]/nombre.js -->
```

## Estructura del proyecto

```
CWU/
├── Controladores/          # Lógica PHP (acciones de formularios, BD)
├── Modelos/                # Modelos de datos PHP
├── Vistas/
│   ├── auth/               # Login, registro
│   ├── cliente/            # Vistas del cliente autenticado
│   ├── monitor/            # Vistas del monitor
│   ├── admin/              # Vistas del administrador
│   └── publico/            # Páginas públicas (info de salas, eventos)
├── database/               # Scripts SQL
├── inc/                    # Fragmentos PHP reutilizables (header, footer, vite.php)
├── src/
│   ├── main.js             # Entry global — Bootstrap, CSS global, librerías
│   ├── css/
│   │   ├── main.css        # Estilos globales y fuentes
│   │   └── index.css       # Estilos de index.php
│   ├── auth/               # JS de login y registro
│   ├── cliente/            # JS y CSS del cliente
│   ├── monitor/            # JS y CSS del monitor
│   └── admin/              # JS y CSS del administrador
├── public/
│   ├── assets/
│   │   ├── img/            # Imágenes del proyecto
│   │   └── fonts/          # Fuentes locales (Poppins, Raleway)
│   └── build/              # Generado por Vite — no subir a git
├── vite.config.js
└── package.json
```

## Tecnologías

| Capa | Tecnología |
|---|---|
| Servidor | PHP + XAMPP |
| Base de datos | MySQL |
| CSS framework | Bootstrap 5 |
| Iconos | Bootstrap Icons |
| Calendario | Flatpickr |
| Bundler | Vite |
