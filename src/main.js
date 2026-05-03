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
