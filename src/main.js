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
