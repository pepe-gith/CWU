const CONTROLADOR = '/cwu/Controladores/AdminControlador.php'
let servicios = []

fetch(`${CONTROLADOR}?action=obtenerServicios`)
    .then(r => r.json())
    .then(data => { if (data.ok) servicios = data.data })
let filtroFecha  = 'proximas'
let filtroEstado = ''
let filtroCliente= ''
let buscarTimer  = null

cargarReservas()

// Filtro fecha
document.querySelectorAll('.filtro-fecha').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filtro-fecha').forEach(b => {
            b.classList.remove('active', 'btn-primary')
            b.classList.add('btn-outline-secondary')
        })
        btn.classList.remove('btn-outline-secondary')
        btn.classList.add('active', 'btn-primary')
        filtroFecha = btn.dataset.filtro
        cargarReservas()
    })
})

// Filtro estado
document.getElementById('filtro-estado').addEventListener('change', e => {
    filtroEstado = e.target.value
    cargarReservas()
})

// Búsqueda cliente con debounce
document.getElementById('filtro-cliente').addEventListener('input', e => {
    clearTimeout(buscarTimer)
    buscarTimer = setTimeout(() => {
        filtroCliente = e.target.value.trim()
        cargarReservas()
    }, 300)
})

// Limpiar filtros
document.getElementById('btn-limpiar').addEventListener('click', () => {
    filtroFecha   = 'proximas'
    filtroEstado  = ''
    filtroCliente = ''
    document.getElementById('filtro-estado').value  = ''
    document.getElementById('filtro-cliente').value = ''
    document.querySelectorAll('.filtro-fecha').forEach(b => {
        b.classList.remove('active', 'btn-primary')
        b.classList.add('btn-outline-secondary')
    })
    document.querySelector('.filtro-fecha[data-filtro="proximas"]').classList.add('active', 'btn-primary')
    document.querySelector('.filtro-fecha[data-filtro="proximas"]').classList.remove('btn-outline-secondary')
    cargarReservas()
})

function cargarReservas() {
    const params = new URLSearchParams({
        action: 'reservas',
        filtro: filtroFecha,
        ...(filtroEstado  && { estado:  filtroEstado }),
        ...(filtroCliente && { cliente: filtroCliente }),
    })

    document.getElementById('tabla-reservas').innerHTML = '<p class="text-muted p-4">Cargando...</p>'

    fetch(`${CONTROLADOR}?${params}`)
        .then(r => r.json())
        .then(data => {
            const contenedor = document.getElementById('tabla-reservas')
            contenedor.innerHTML = data.data.length
                ? renderTabla(data.data)
                : '<p class="text-muted p-4">No hay reservas con esos filtros.</p>'

            contenedor.querySelectorAll('.select-estado').forEach(sel =>
                sel.addEventListener('change', () => cambiarEstado(sel.dataset.id, sel.value, sel))
            )
            contenedor.querySelectorAll('.btn-gestionar-cambio').forEach(btn =>
                btn.addEventListener('click', () => gestionarCambio(btn.dataset.id))
            )
            contenedor.querySelectorAll('.btn-editar-reserva').forEach(btn =>
                btn.addEventListener('click', () => abrirEditar(JSON.parse(btn.dataset.r)))
            )
        })
        .catch(() => {
            document.getElementById('tabla-reservas').innerHTML = '<p class="text-danger p-4">Error al cargar.</p>'
        })
}

let cancelPendiente = null

function cambiarEstado(id, estado, selectEl) {
    if (estado === 'cancelada') {
        cancelPendiente = { id, selectEl }
        document.getElementById('admin-motivo-cancelacion').value = ''
        window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalMotivoCancelacion')).show()
        return
    }
    enviarCambioEstado(id, estado, null, selectEl)
}

function enviarCambioEstado(id, estado, motivo, selectEl) {
    const fd = new FormData()
    fd.append('action', 'cambiarEstadoReserva')
    fd.append('id', id)
    fd.append('estado', estado)
    if (motivo) fd.append('motivo', motivo)
    selectEl.disabled = true

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) cargarReservas()
            else { window.mostrarToast(data.error, 'danger'); selectEl.value = selectEl.dataset.estadoAnterior }
        })
        .finally(() => { selectEl.disabled = false })
}

document.getElementById('btn-confirmar-cancelacion-admin').addEventListener('click', () => {
    if (!cancelPendiente) return
    const motivo = document.getElementById('admin-motivo-cancelacion').value.trim()
    if (!motivo) { window.mostrarToast('El motivo es obligatorio', 'warning'); return }

    window.bootstrap.Modal.getInstance(document.getElementById('modalMotivoCancelacion')).hide()
    enviarCambioEstado(cancelPendiente.id, 'cancelada', motivo, cancelPendiente.selectEl)
    cancelPendiente = null
})

document.getElementById('modalMotivoCancelacion').addEventListener('hidden.bs.modal', () => {
    if (cancelPendiente) {
        cancelPendiente.selectEl.value = cancelPendiente.selectEl.dataset.estadoAnterior ?? 'pendiente'
        cancelPendiente = null
    }
})

function opcionesEstadoReserva(estado) {
    const transiciones = {
        pendiente:  ['pendiente', 'confirmada', 'cancelada'],
        confirmada: ['confirmada', 'cancelada'],
        cancelada:  ['cancelada', 'pendiente'],
    }
    const nombres = { pendiente: 'Pendiente', confirmada: 'Confirmada', cancelada: 'Cancelada' }
    return (transiciones[estado] ?? [estado]).map(e =>
        `<option value="${e}" ${e === estado ? 'selected' : ''}>${nombres[e]}</option>`
    ).join('')
}

function abrirEditar(r) {
    const form = document.getElementById('form-editar-reserva')
    form.elements['id'].value            = r.id
    form.elements['fecha_evento'].value  = r.fecha_evento
    form.elements['hora_inicio'].value   = r.hora_inicio.slice(0, 5)
    form.elements['hora_fin'].value      = r.hora_fin.slice(0, 5)
    form.elements['num_asistentes'].value = r.num_asistentes
    form.elements['observaciones'].value = r.observaciones ?? ''

    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarReserva')).show()
}

document.getElementById('form-editar-reserva').addEventListener('submit', e => {
    e.preventDefault()
    const fd = new FormData(e.target)
    fd.append('action', 'editarReserva')

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.bootstrap.Modal.getInstance(document.getElementById('modalEditarReserva')).hide()
            window.mostrarToast(data.mensaje, 'success')
            cargarReservas()
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'))
})

function gestionarCambio(id) {
    const fd = new FormData()
    fd.append('action', 'gestionarCambio')
    fd.append('id', id)

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast(data.mensaje, 'success')
            cargarReservas()
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'))
}

function renderTabla(items) {
    const filas = items.map(r => `
        <tr>
            <td class="text-muted">#${r.id}</td>
            <td>
                <div class="fw-medium">${r.cliente} ${r.apellidos}</div>
                <div class="text-muted small">${r.telefono ?? ''}</div>
            </td>
            <td>${r.servicio}</td>
            <td>${r.fecha_evento}</td>
            <td>${r.hora_inicio.slice(0,5)} – ${r.hora_fin.slice(0,5)}</td>
            <td>${r.num_asistentes}</td>
            <td>${r.observaciones ? `<span class="text-muted small">${r.observaciones}</span>` : '—'}</td>
            <td>${r.motivo_cancelacion ? `<span class="text-danger small"><i class="bi bi-x-circle"></i> ${r.motivo_cancelacion}</span>` : '—'}</td>
            <td>${r.cambio_solicitado == 1 ? `
                    <div class="mt-1">
                        <span class="badge bg-warning text-dark">Cambio solicitado</span>
                        <div class="text-muted small">${r.motivo_cambio}</div>
                        <button class="btn btn-xs btn-outline-secondary btn-gestionar-cambio mt-1" data-id="${r.id}" style="font-size:.75rem;padding:.1rem .4rem">
                            Marcar como gestionado
                        </button>
                    </div>` : ''}
            </td>
            <td>
                <select class="select-estado badge-estado badge-reserva-${r.estado}" data-id="${r.id}">
                    ${opcionesEstadoReserva(r.estado)}
                </select>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-secondary btn-editar-reserva"
                    data-r='${JSON.stringify(r).replace(/'/g, "&#39;")}'>
                    <i class="bi bi-pencil"></i>
                </button>
            </td>
        </tr>
    `).join('')

    return `
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Fecha evento</th>
                        <th>Horario</th>
                        <th>Asistentes</th>
                        <th>Observaciones</th>
                        <th>Motivo cancelación</th>
                        <th>Cambio solicitado</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}
