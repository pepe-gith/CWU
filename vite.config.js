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
                'admin/admin':        'src/admin/admin.js',
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
