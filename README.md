# CWU - Celebrate With Us

Aplicación web para gestión de reservas de eventos y escape rooms.

## Requisitos

- [XAMPP](https://www.apachefriends.org/) (PHP + MySQL)
- [Node.js](https://nodejs.org/) v22 LTS o superior

## Instalación

```bash
# 1. Clona el repositorio en htdocs de XAMPP
git clone <url-repo> c:/xampp/htdocs/CWU

# 2. Instala las dependencias y genera los assets
npm install
npm run build
```

> `npm install` descarga las librerías. `npm run build` las empaqueta en `public/build/`.

## Desarrollo

```bash
# Generar assets para producción (ejecutar cada vez que cambies librerías)
npm run build

# Modo desarrollo con hot reload (opcional)
npm run dev
```

Con `npm run dev` activo, los cambios en `src/main.js` se reflejan en el navegador sin hacer build.

## Añadir una librería nueva

```bash
# 1. Instalar con npm
npm install nombre-libreria

# 2. Importarla en src/main.js
import 'nombre-libreria/dist/archivo.css'
import 'nombre-libreria'

# 3. Regenerar el build
npm run build
```

## Inicialización de JS en las vistas

El script de Vite es `type="module"` (asíncrono). Los inline scripts en los PHP
se ejecutan antes de que el módulo cargue, por lo que **no pueden llamar a funciones
de las librerías directamente**.

**❌ No hacer — inline script en el PHP:**
```html
<script>
    flatpickr("#cal", { ... }) // Error: flatpickr is not defined
</script>
```

**✅ Correcto — inicializar en `src/main.js` con check del elemento:**
```js
const cal = document.getElementById('cal')
if (cal) {
    flatpickr(cal, { ... })
}
```

## Configuración importante

El `vite.config.js` tiene `base: '/cwu/public/build/'` para que las rutas de
fuentes e imágenes generadas por Vite apunten a la ruta correcta en XAMPP.
Si el proyecto se sirve desde otra ruta, hay que actualizar este valor.

## Estructura del proyecto

```
CWU/
├── Controladores/      # Lógica PHP (acciones de formularios, BD)
├── Modelos/            # Modelos de datos PHP
├── Vistas/             # Páginas PHP (vistas del cliente)
├── CSS/                # Estilos propios del proyecto
├── js/                 # Scripts JS propios del proyecto
├── fonts/              # Fuentes locales (Poppins, Raleway)
├── inc/                # Fragmentos PHP reutilizables (header, footer, vite.php)
├── src/
│   └── main.js         # Entrada de Vite — importa todas las librerías
├── public/
│   └── build/          # Assets generados por Vite (no subir a git)
├── libs/               # Librerías generadas por npm install (no subir a git)
├── vite.config.js      # Configuración de Vite
├── package.json        # Dependencias del proyecto
└── .gitignore
```

## Tecnologías

| Capa | Tecnología |
|---|---|
| Servidor | PHP + XAMPP |
| Base de datos | MySQL |
| CSS framework | Bootstrap 5 |
| Iconos | Bootstrap Icons |
| Calendario cliente | Flatpickr |
| Calendario admin | FullCalendar |
| Bundler | Vite |
