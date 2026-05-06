import { validarNIF } from '../utils/validarNIF'

const contenedor = document.getElementById('tabla-usuarios')
const buscador   = document.getElementById('buscador')

let rolActivo = ''
let timer = null
let rolesMap = {}
let usuarioActual = null

const BADGE_ESTADO = {
    pendiente:     'bg-warning text-dark',
    presupuestada: 'bg-info text-dark',
    aceptada:      'bg-success',
    reservada:     'bg-purple',
    rechazada:     'bg-danger',
}
const BADGE_RESERVA = {
    pendiente:  'bg-warning text-dark',
    confirmada: 'bg-success',
    cancelada:  'bg-danger',
}

function badgeEstado(estado) {
    return `<span class="badge ${BADGE_ESTADO[estado] ?? 'bg-secondary'}">${estado}</span>`
}
function badgeReserva(estado) {
    return `<span class="badge ${BADGE_RESERVA[estado] ?? 'bg-secondary'}">${estado}</span>`
}

const BADGE_COLORS = ['bg-danger', 'bg-primary', 'bg-success', 'bg-warning', 'bg-info']

function badgeRol(idRol) {
    const nombre = rolesMap[idRol] ?? `Rol ${idRol}`
    const color  = BADGE_COLORS[(idRol - 1) % BADGE_COLORS.length]
    return `<span class="badge ${color}">${nombre}</span>`
}

// ── Roles ────────────────────────────────────────────────────────────────────

function cargarRoles() {
    return fetch('/cwu/Controladores/AdminControlador.php?action=roles')
        .then(r => r.json())
        .then(data => {
            if (!data.ok) return
            rolesMap = Object.fromEntries(data.data.map(r => [r.id, r.nombre_rol]))
            renderFiltros(data.data)
        })
}

function renderFiltros(roles) {
    const wrap = document.getElementById('filtros-rol')
    roles.forEach(r => {
        const btn = document.createElement('button')
        btn.className = 'btn btn-sm btn-outline-secondary filtro-rol'
        btn.dataset.rol = r.id
        btn.textContent = r.nombre_rol
        wrap.querySelector('.ms-auto').insertAdjacentElement('beforebegin', btn)
    })

    wrap.querySelectorAll('.filtro-rol, .btn-todos').forEach(btn => {
        btn.addEventListener('click', () => {
            wrap.querySelectorAll('.filtro-rol, .btn-todos').forEach(b => {
                b.classList.remove('active', 'btn-primary')
                b.classList.add('btn-outline-secondary')
            })
            btn.classList.add('active', 'btn-primary')
            btn.classList.remove('btn-outline-secondary')
            rolActivo = btn.dataset.rol ?? ''
            cargar()
        })
    })
}

// ── Tabla ────────────────────────────────────────────────────────────────────

function cargar() {
    const params = new URLSearchParams({ action: 'usuarios' })
    if (rolActivo) params.set('rol', rolActivo)
    const q = buscador.value.trim()
    if (q) params.set('buscar', q)

    contenedor.innerHTML = '<p class="text-muted p-4">Cargando...</p>'

    fetch('/cwu/Controladores/AdminControlador.php?' + params)
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { contenedor.innerHTML = '<p class="text-danger p-4">Error al cargar.</p>'; return }
            if (!data.data.length) { contenedor.innerHTML = '<p class="text-muted p-4">No se encontraron usuarios.</p>'; return }
            contenedor.innerHTML = renderTabla(data.data)
            contenedor.querySelectorAll('.btn-ver').forEach(btn =>
                btn.addEventListener('click', () => abrirModal(JSON.parse(btn.dataset.u)))
            )
        })
        .catch(() => { contenedor.innerHTML = '<p class="text-danger p-4">Error de conexión.</p>' })
}

function renderTabla(usuarios) {
    const filas = usuarios.map(u => `
        <tr class="${u.activo == 0 ? 'table-secondary text-muted' : ''}">
            <td>${u.nif}</td>
            <td>${u.nombre} ${u.apellidos} ${u.activo == 0 ? '<span class="badge bg-secondary ms-1">Inactivo</span>' : ''}</td>
            <td>${u.email}</td>
            <td>${u.telefono ?? '—'}</td>
            <td>${badgeRol(u.id_rol)}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary btn-ver"
                    data-u='${JSON.stringify(u).replace(/'/g, "&#39;")}'>Ver</button>
            </td>
        </tr>
    `).join('')

    return `
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>NIF</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Rol</th><th></th></tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}

// ── Modal ────────────────────────────────────────────────────────────────────

function abrirModal(u) {
    usuarioActual = u
    const esPropio = u.id == window.SESSION_ID

    document.getElementById('modal-usuario-titulo').textContent = `${u.nombre} ${u.apellidos}`

    // Pestaña Datos
    const form = document.getElementById('form-editar-usuario')
    form.elements['id'].value        = u.id
    form.elements['nombre'].value    = u.nombre
    form.elements['apellidos'].value = u.apellidos
    form.elements['email'].value     = u.email
    form.elements['telefono'].value  = u.telefono ?? ''
    document.getElementById('datos-nif').textContent = u.nif

    // Pestaña Acceso — rol
    const selectRol = document.getElementById('select-rol-modal')
    selectRol.innerHTML = Object.entries(rolesMap)
        .map(([id, nombre]) => `<option value="${id}" ${u.id_rol == id ? 'selected' : ''}>${nombre}</option>`)
        .join('')
    selectRol.disabled = esPropio

    // Pestaña Acceso — activo
    document.getElementById('badge-activo').innerHTML = u.activo == 1
        ? '<span class="badge bg-success">Activo</span>'
        : '<span class="badge bg-secondary">Inactivo</span>'

    const btnToggle = document.getElementById('btn-toggle-activo')
    btnToggle.textContent = u.activo == 1 ? 'Desactivar usuario' : 'Activar usuario'
    btnToggle.className   = u.activo == 1 ? 'btn btn-outline-danger' : 'btn btn-outline-success'
    btnToggle.disabled    = esPropio

    document.getElementById('btn-cambiar-rol').disabled = esPropio

    // Pestaña Historial — carga al abrir
    cargarHistorial(u.id)

    // Mostrar/ocultar pestaña historial según rol
    const tabHistorial = document.querySelector('#tabs-usuario .nav-link[data-bs-target="#tab-historial"]').closest('li')
    tabHistorial.classList.toggle('d-none', u.id_rol == 1)

    // Resetear a primera pestaña
    window.bootstrap.Tab.getOrCreateInstance(
        document.querySelector('#tabs-usuario .nav-link')
    ).show()

    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUsuario')).show()
}

function cargarHistorial(idUsuario) {
    const wrap = document.getElementById('historial-contenido')
    wrap.innerHTML = '<p class="text-muted">Cargando...</p>'

    fetch(`/cwu/Controladores/AdminControlador.php?action=historialUsuario&id=${idUsuario}`)
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { wrap.innerHTML = '<p class="text-danger">Error al cargar.</p>'; return }

            if (data.rol === 'admin') {
                wrap.innerHTML = '<p class="text-muted">No hay historial para administradores.</p>'
                return
            }

            if (data.rol === 'empleado') {
                const filas = data.asignaciones.length
                    ? data.asignaciones.map(a => `
                        <tr>
                            <td>${a.fecha_evento}</td>
                            <td>${a.hora_inicio.slice(0,5)}–${a.hora_fin.slice(0,5)}</td>
                            <td>${a.servicio}</td>
                            <td>${{ pendiente: '<span class="badge bg-warning text-dark">Pendiente</span>', aceptada: '<span class="badge bg-success">Aceptada</span>', rechazada: '<span class="badge bg-danger">Rechazada</span>' }[a.estado] ?? ''}</td>
                        </tr>`).join('')
                    : '<tr><td colspan="4" class="text-muted">Sin asignaciones</td></tr>'

                wrap.innerHTML = `
                    <h6 class="mt-2">Asignaciones recientes <span class="text-muted fw-normal small">(últimas 5)</span></h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light"><tr><th>Fecha</th><th>Horario</th><th>Servicio</th><th>Estado</th></tr></thead>
                            <tbody>${filas}</tbody>
                        </table>
                    </div>`
                return
            }

            const solHtml = data.solicitudes.length
                ? data.solicitudes.map(s => `
                    <tr><td>${s.fecha_evento}</td><td>${s.tipo}</td>
                    <td>${badgeEstado(s.estado)}</td></tr>`).join('')
                : '<tr><td colspan="3" class="text-muted">Sin solicitudes</td></tr>'

            const resHtml = data.reservas.length
                ? data.reservas.map(r => `
                    <tr><td>${r.fecha_evento}</td><td>${r.hora_inicio.slice(0,5)}–${r.hora_fin.slice(0,5)}</td>
                    <td>${r.servicio}</td>
                    <td>${badgeReserva(r.estado)}</td></tr>`).join('')
                : '<tr><td colspan="4" class="text-muted">Sin reservas</td></tr>'

            wrap.innerHTML = `
                <h6 class="mt-2">Solicitudes recientes <span class="text-muted fw-normal small">(últimas 5)</span></h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle">
                        <thead class="table-light"><tr><th>Fecha</th><th>Tipo</th><th>Estado</th></tr></thead>
                        <tbody>${solHtml}</tbody>
                    </table>
                </div>
                <h6>Reservas recientes <span class="text-muted fw-normal small">(últimas 5)</span></h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light"><tr><th>Fecha</th><th>Horario</th><th>Servicio</th><th>Estado</th></tr></thead>
                        <tbody>${resHtml}</tbody>
                    </table>
                </div>`
        })
        .catch(() => { wrap.innerHTML = '<p class="text-danger">Error de conexión.</p>' })
}

// ── Eventos formulario ───────────────────────────────────────────────────────

document.getElementById('form-editar-usuario').addEventListener('submit', e => {
    e.preventDefault()
    const fd = new FormData(e.target)
    fd.append('action', 'editarUsuario')

    fetch('/cwu/Controladores/AdminControlador.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast('Datos actualizados', 'success')
            cargar()
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'))
})

document.getElementById('btn-cambiar-rol').addEventListener('click', () => {
    if (!usuarioActual) return
    ejecutarCambioRol(false)
})

document.getElementById('btn-confirmar-cambio-rol').addEventListener('click', () => {
    window.bootstrap.Modal.getInstance(document.getElementById('modalConfirmarCambioRol')).hide()
    ejecutarCambioRol(true)
})

function ejecutarCambioRol(forzar) {
    const nuevoRol = parseInt(document.getElementById('select-rol-modal').value)

    const fd = new FormData()
    fd.append('action', 'cambiarRol')
    fd.append('id', usuarioActual.id)
    fd.append('rol', nuevoRol)
    if (forzar) fd.append('forzar', '1')

    fetch('/cwu/Controladores/AdminControlador.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.confirmar) {
                const parts = []
                if (data.solicitudes) parts.push(`${data.solicitudes} solicitud${data.solicitudes > 1 ? 'es' : ''} activa${data.solicitudes > 1 ? 's' : ''}`)
                if (data.reservas) parts.push(`${data.reservas} reserva${data.reservas > 1 ? 's' : ''} confirmada${data.reservas > 1 ? 's' : ''}`)
                document.getElementById('texto-confirmar-rol').textContent =
                    `Este usuario tiene ${parts.join(' y ')}. Al cambiar el rol perderá acceso al área de cliente.`
                window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalConfirmarCambioRol')).show()
                return
            }
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast(`Rol actualizado a ${rolesMap[nuevoRol]}`, 'success')
            usuarioActual.id_rol = nuevoRol
            cargar()
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'))
}

document.getElementById('btn-toggle-activo').addEventListener('click', () => {
    if (!usuarioActual) return

    const fd = new FormData()
    fd.append('action', 'toggleActivo')
    fd.append('id', usuarioActual.id)

    fetch('/cwu/Controladores/AdminControlador.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.confirmar) {
                // Empleado con reservas futuras — mostrar aviso
                const lista = document.getElementById('lista-reservas-afectadas')
                lista.innerHTML = data.reservas.map(r =>
                    `<li class="list-group-item py-1 px-0">
                        <span class="fw-medium">${r.fecha_evento}</span>
                        <span class="text-muted ms-2">${r.servicio}</span>
                        <span class="text-muted small ms-1">(#${r.id})</span>
                    </li>`
                ).join('')
                window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalConfirmarDesactivar')).show()
                return
            }
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            aplicarToggle(data)
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'))
})

document.getElementById('btn-confirmar-desactivar').addEventListener('click', () => {
    if (!usuarioActual) return

    const fd = new FormData()
    fd.append('action', 'confirmarDesactivar')
    fd.append('id', usuarioActual.id)

    window.bootstrap.Modal.getInstance(document.getElementById('modalConfirmarDesactivar')).hide()

    fetch('/cwu/Controladores/AdminControlador.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            aplicarToggle(data)
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'))
})

function aplicarToggle(data) {
    usuarioActual.activo = data.activo ? 1 : 0
    document.getElementById('badge-activo').innerHTML = data.activo
        ? '<span class="badge bg-success">Activo</span>'
        : '<span class="badge bg-secondary">Inactivo</span>'
    const btn = document.getElementById('btn-toggle-activo')
    btn.textContent = data.activo ? 'Desactivar usuario' : 'Activar usuario'
    btn.className   = data.activo ? 'btn btn-outline-danger' : 'btn btn-outline-success'
    window.mostrarToast(data.mensaje, data.activo ? 'success' : 'warning')
    cargar()
}

buscador.addEventListener('input', () => {
    clearTimeout(timer)
    timer = setTimeout(cargar, 350)
})

// ── Nuevo usuario ────────────────────────────────────────────────────────────

document.getElementById('btn-nuevo-usuario').addEventListener('click', () => {
    document.getElementById('form-nuevo-usuario').reset()
    const sel = document.getElementById('select-rol-nuevo')
    const clienteId = Object.entries(rolesMap).find(([, nombre]) => nombre === 'cliente')?.[0]
    sel.innerHTML = Object.entries(rolesMap)
        .map(([id, nombre]) => `<option value="${id}" ${id === clienteId ? 'selected' : ''}>${nombre}</option>`).join('')
    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalNuevoUsuario')).show()
})

const rePassword = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/

function verificarCampoUnico(campo, valor, input) {
    if (!valor) return
    const fd = new FormData()
    fd.append('action', 'verificarCampo')
    fd.append('campo', campo)
    fd.append('valor', valor)
    fetch('/cwu/Controladores/UsuarioControlador.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.existe) {
                input.classList.add('is-invalid')
                const fb = input.nextElementSibling
                if (fb?.classList.contains('invalid-feedback'))
                    fb.textContent = `Este ${campo === 'nif' ? 'NIF' : 'email'} ya está registrado.`
            }
        })
}

document.getElementById('nuevo-nif').addEventListener('blur', function () {
    const ok = validarNIF(this.value)
    this.classList.toggle('is-invalid', !ok)
    if (ok) verificarCampoUnico('nif', this.value, this)
})

document.getElementById('nuevo-email').addEventListener('blur', function () {
    if (this.value) verificarCampoUnico('email', this.value, this)
})

document.getElementById('nuevo-password').addEventListener('input', function () {
    const ok = rePassword.test(this.value)
    this.classList.toggle('is-invalid', this.value.length > 0 && !ok)
    this.classList.toggle('is-valid', ok)
})

document.getElementById('nuevo-password-confirm').addEventListener('input', function () {
    const pass = document.getElementById('nuevo-password').value
    this.classList.toggle('is-invalid', this.value.length > 0 && this.value !== pass)
    this.classList.toggle('is-valid', this.value === pass && this.value.length > 0)
})

document.getElementById('form-nuevo-usuario').addEventListener('submit', e => {
    e.preventDefault()
    const nif     = document.getElementById('nuevo-nif')
    const pass    = document.getElementById('nuevo-password')
    const confirm = document.getElementById('nuevo-password-confirm')
    let error = false

    if (!validarNIF(nif.value)) { nif.classList.add('is-invalid'); error = true }
    if (!rePassword.test(pass.value)) { pass.classList.add('is-invalid'); error = true }
    if (pass.value !== confirm.value) { confirm.classList.add('is-invalid'); error = true }
    if (error) return

    const fd = new FormData(e.target)
    fd.append('action', 'crearUsuario')

    fetch('/cwu/Controladores/AdminControlador.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.bootstrap.Modal.getInstance(document.getElementById('modalNuevoUsuario')).hide()
            window.mostrarToast('Usuario creado correctamente', 'success')
            cargar()
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'))
})

// ── Init ─────────────────────────────────────────────────────────────────────
cargarRoles().then(cargar)
