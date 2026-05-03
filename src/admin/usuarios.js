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
                <h6 class="mt-2">Solicitudes recientes</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle">
                        <thead class="table-light"><tr><th>Fecha</th><th>Tipo</th><th>Estado</th></tr></thead>
                        <tbody>${solHtml}</tbody>
                    </table>
                </div>
                <h6>Reservas recientes</h6>
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
    const nuevoRol = parseInt(document.getElementById('select-rol-modal').value)

    const fd = new FormData()
    fd.append('action', 'cambiarRol')
    fd.append('id', usuarioActual.id)
    fd.append('rol', nuevoRol)

    fetch('/cwu/Controladores/AdminControlador.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast(`Rol actualizado a ${rolesMap[nuevoRol]}`, 'success')
            cargar()
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'))
})

document.getElementById('btn-toggle-activo').addEventListener('click', () => {
    if (!usuarioActual) return

    const fd = new FormData()
    fd.append('action', 'toggleActivo')
    fd.append('id', usuarioActual.id)

    fetch('/cwu/Controladores/AdminControlador.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            usuarioActual.activo = data.activo ? 1 : 0
            document.getElementById('badge-activo').innerHTML = data.activo
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-secondary">Inactivo</span>'
            const btn = document.getElementById('btn-toggle-activo')
            btn.textContent = data.activo ? 'Desactivar usuario' : 'Activar usuario'
            btn.className   = data.activo ? 'btn btn-outline-danger' : 'btn btn-outline-success'
            window.mostrarToast(data.mensaje, data.activo ? 'success' : 'warning')
            cargar()
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'))
})

buscador.addEventListener('input', () => {
    clearTimeout(timer)
    timer = setTimeout(cargar, 350)
})

// ── Init ─────────────────────────────────────────────────────────────────────
cargarRoles().then(cargar)
