import { defineConfig } from 'vite'

export default defineConfig(({ command }) => ({
    base: command === 'build' ? '/cwu/public/build/' : '/',
    publicDir: false,
    build: {
        outDir: 'public/build',
        manifest: 'manifest.json',
        rollupOptions: {
            input: {
                main:                'src/main.js',
                index:               'src/index.js',
                'auth/acceso':       'src/auth/acceso.js',
                'auth/registro':     'src/auth/registro.js',
                'cliente/inicio':      'src/cliente/inicio.js',
                'cliente/solicitudes':'src/cliente/solicitudes.js',
                'cliente/solEvento':  'src/cliente/solEvento.js',
                'cliente/reservas':   'src/cliente/reservas.js',
                'admin/admin':        'src/admin/admin.js',
                'admin/usuarios':     'src/admin/usuarios.js',
                'admin/empleados':    'src/admin/empleados.js',
                'admin/dashboard':    'src/admin/dashboard.js',
                'admin/solicitudes':  'src/admin/solicitudes.js',
                'admin/reservas':     'src/admin/reservas.js',
                'admin/servicios':    'src/admin/servicios.js',
                'admin/categorias':   'src/admin/categorias.js',
                'admin/calendario':   'src/admin/calendario.js',
                'empleado/agenda':    'src/empleado/agenda.js',
                'empleado/historial': 'src/empleado/historial.js',
                'perfil/perfil':      'src/perfil/perfil.js',
            }
        }
    },
    server: {
        port: 5173,
        strictPort: true,
        cors: true,
        origin: 'http://localhost:5173'
    }
}))
