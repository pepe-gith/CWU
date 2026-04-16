import { defineConfig } from 'vite'

export default defineConfig({
    base: '/cwu/public/build/',
    publicDir: false,
    build: {
        outDir: 'public/build',
        manifest: 'manifest.json',
        rollupOptions: {
            input: {
                main: 'src/main.js'
            }
        }
    },
    server: {
        cors: true
    }
})
