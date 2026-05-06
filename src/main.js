// CSS global
import './css/main.css'

// CSS de librerías
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.min.css'
import 'flatpickr/dist/flatpickr.min.css'

// JS de librerías
import * as bootstrap from 'bootstrap'
import flatpickr from 'flatpickr'
import { Spanish } from 'flatpickr/dist/l10n/es.js'

// Exponer globalmente
window.bootstrap = bootstrap
window.flatpickr = flatpickr
flatpickr.localize(Spanish)

// Notificaciones internas (clientes y empleados)
document.addEventListener('DOMContentLoaded', () => {
    if (!window.ID_ROL) return
    const wrap = document.getElementById('notificaciones-wrap')
    if (!wrap) return

    fetch('/cwu/Controladores/UsuarioControlador.php?action=obtenerNotificaciones')
        .then(r => r.json())
        .then(data => {
            if (!data.ok || !data.data.length) return

            // Badge en el menú
            const badges = document.querySelectorAll('.notif-badge')
            badges.forEach(b => { b.textContent = data.data.length; b.classList.remove('d-none') })

            // Alertas en el contenido
            data.data.forEach(n => {
                const div = document.createElement('div')
                div.className = 'alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mx-4 mt-3 mb-0'
                div.innerHTML = `<i class="bi bi-bell-fill flex-shrink-0"></i><span>${n.mensaje}</span><button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>`
                div.querySelector('.btn-close').addEventListener('click', () => {
                    const fd = new FormData()
                    fd.append('action', 'marcarNotificacionLeida')
                    fd.append('id', n.id)
                    fetch('/cwu/Controladores/UsuarioControlador.php', { method: 'POST', body: fd })
                    // Actualizar badge
                    const remaining = wrap.querySelectorAll('.alert').length - 1
                    badges.forEach(b => {
                        if (remaining <= 0) b.classList.add('d-none')
                        else b.textContent = remaining
                    })
                })
                wrap.appendChild(div)
            })
        })
        .catch(() => {})
})

// Toast global
window.mostrarToast = function(mensaje, tipo = 'danger') {
    let contenedor = document.getElementById('toast-contenedor')
    if (!contenedor) {
        contenedor = document.createElement('div')
        contenedor.id = 'toast-contenedor'
        contenedor.style.cssText = 'position:fixed;top:1.5rem;right:1.5rem;z-index:9999;display:flex;flex-direction:column;gap:.5rem;'
        document.body.appendChild(contenedor)
    }

    const toast = document.createElement('div')
    toast.className = `alert alert-${tipo} alert-dismissible shadow mb-0`
    toast.style.minWidth = '280px'
    toast.innerHTML = `${mensaje}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`
    contenedor.appendChild(toast)

    setTimeout(() => toast.remove(), 5000)
}
