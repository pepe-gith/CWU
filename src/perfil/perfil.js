const CONTROLADOR = '/cwu/Controladores/UsuarioControlador.php'

// Cargar datos al inicio
fetch(`${CONTROLADOR}?action=obtenerPerfil`)
    .then(r => r.json())
    .then(data => {
        if (!data.ok) return mostrarAlerta('danger', data.error)
        const u = data.data
        document.getElementById('nombre').value       = u.nombre        ?? ''
        document.getElementById('apellidos').value    = u.apellidos     ?? ''
        document.getElementById('nif').value          = u.nif           ?? ''
        document.getElementById('email').value        = u.email         ?? ''
        document.getElementById('telefono').value     = u.telefono      ?? ''
        document.getElementById('otro_telefono').value= u.otro_telefono ?? ''
        document.getElementById('direccion').value    = u.direccion     ?? ''
    })
    .catch(() => mostrarAlerta('danger', 'Error al cargar los datos del perfil'))

// Guardar datos personales
document.getElementById('form-perfil').addEventListener('submit', async e => {
    e.preventDefault()
    const spinner = document.getElementById('spinner-perfil')
    const btn     = document.getElementById('btn-guardar')
    spinner.classList.remove('d-none')
    btn.disabled = true

    try {
        const body = new FormData(e.target)
        body.append('action', 'actualizarPerfil')
        const res  = await fetch(CONTROLADOR, { method: 'POST', body })
        const data = await res.json()
        if (data.ok) mostrarAlerta('success', data.mensaje)
        else         mostrarAlerta('danger',  data.error)
    } catch {
        mostrarAlerta('danger', 'Error de conexión')
    } finally {
        spinner.classList.add('d-none')
        btn.disabled = false
    }
})

// Cambiar contraseña
document.getElementById('form-password').addEventListener('submit', async e => {
    e.preventDefault()
    const spinner = document.getElementById('spinner-password')
    const btn     = document.getElementById('btn-password')

    const nueva     = e.target.password_nueva.value
    const confirmar = e.target.password_confirmar.value
    if (nueva !== confirmar) {
        mostrarAlerta('danger', 'Las contraseñas no coinciden')
        return
    }

    spinner.classList.remove('d-none')
    btn.disabled = true

    try {
        const body = new FormData(e.target)
        body.append('action', 'cambiarPassword')
        const res  = await fetch(CONTROLADOR, { method: 'POST', body })
        const data = await res.json()
        if (data.ok) {
            mostrarAlerta('success', data.mensaje)
            e.target.reset()
        } else {
            mostrarAlerta('danger', data.error)
        }
    } catch {
        mostrarAlerta('danger', 'Error de conexión')
    } finally {
        spinner.classList.add('d-none')
        btn.disabled = false
    }
})

function mostrarAlerta(tipo, mensaje) {
    const el = document.getElementById('perfil-alert')
    el.innerHTML = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' })
}
