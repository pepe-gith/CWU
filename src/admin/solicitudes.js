const CONTROLADOR = '/cwu/Controladores/AdminControlador.php'
let estadoActivo  = ''
let modalReserva  = null

cargarSolicitudes('')
cargarServicios()

// Filtros
document.querySelectorAll('.filtro-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filtro-btn').forEach(b => {
            b.classList.remove('active', 'btn-primary', 'btn-warning', 'btn-info', 'btn-success', 'btn-danger')
            b.classList.add('btn-outline-' + (colorEstado(b.dataset.estado) || 'primary'))
        })
        btn.classList.remove('btn-outline-' + (colorEstado(btn.dataset.estado) || 'primary'))
        btn.classList.add('active', 'btn-' + (colorEstado(btn.dataset.estado) || 'primary'))
        estadoActivo = btn.dataset.estado
        cargarSolicitudes(estadoActivo)
    })
})

// Modal presupuesto
let selectPendiente = null
document.getElementById('modalPresupuesto').addEventListener('hidden.bs.modal', () => {
    if (selectPendiente) {
        selectPendiente.value = selectPendiente.dataset.estadoAnterior
        selectPendiente.className = `select-estado badge-estado badge-${selectPendiente.dataset.estadoAnterior}`
        selectPendiente = null
    }
})

document.getElementById('form-presupuesto').addEventListener('submit', async e => {
    e.preventDefault()
    const fd = new FormData(e.target)
    fd.append('action', 'cambiarEstado')
    fd.append('estado', 'presupuestada')

    try {
        const res  = await fetch(CONTROLADOR, { method: 'POST', body: fd })
        const data = await res.json()
        if (data.ok) {
            if (selectPendiente) {
                selectPendiente.className = 'select-estado badge-estado badge-presupuestada'
                selectPendiente.dataset.estadoAnterior = 'presupuestada'
                selectPendiente = null
            }
            window.bootstrap.Modal.getInstance(document.getElementById('modalPresupuesto')).hide()
            cargarSolicitudes(estadoActivo)
        } else {
            alert(data.error)
        }
    } catch {
        window.mostrarToast('Error de conexión')
    }
})

// Modal reserva
document.getElementById('modalReserva').addEventListener('shown.bs.modal', () => {
    document.getElementById('modal-alert').innerHTML = ''
})

document.getElementById('form-reserva').addEventListener('submit', async e => {
    e.preventDefault()
    const spinner = document.getElementById('spinner-reserva')
    const btn     = document.getElementById('btn-crear-reserva')
    spinner.classList.remove('d-none')
    btn.disabled = true

    try {
        const fd = new FormData(e.target)
        fd.append('action', 'crearReserva')
        const res  = await fetch(CONTROLADOR, { method: 'POST', body: fd })
        const data = await res.json()
        if (data.ok) {
            window.bootstrap.Modal.getInstance(document.getElementById('modalReserva')).hide()
            cargarSolicitudes(estadoActivo)
        } else {
            document.getElementById('modal-alert').innerHTML =
                `<div class="alert alert-danger">${data.error}</div>`
        }
    } catch {
        document.getElementById('modal-alert').innerHTML =
            '<div class="alert alert-danger">Error de conexión</div>'
    } finally {
        spinner.classList.add('d-none')
        btn.disabled = false
    }
})

function cargarSolicitudes(estado) {
    const url = `${CONTROLADOR}?action=solicitudes${estado ? '&estado=' + estado : ''}`
    document.getElementById('tabla-solicitudes').innerHTML = '<p class="text-muted p-4">Cargando...</p>'

    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (!data.ok) return
            const contenedor = document.getElementById('tabla-solicitudes')
            contenedor.innerHTML = data.data.length
                ? renderTabla(data.data)
                : '<p class="text-muted p-4">No hay solicitudes.</p>'

            contenedor.querySelectorAll('.select-estado').forEach(sel => {
                sel.addEventListener('change', () => cambiarEstado(sel.dataset.id, sel.value, sel))
            })

            contenedor.querySelectorAll('.btn-crear-reserva').forEach(btn => {
                btn.addEventListener('click', () => abrirModalReserva(btn))
            })
        })
        .catch(() => {
            document.getElementById('tabla-solicitudes').innerHTML = '<p class="text-danger p-4">Error al cargar.</p>'
        })
}

function cargarServicios() {
    fetch(`${CONTROLADOR}?action=obtenerServicios`)
        .then(r => r.json())
        .then(data => {
            if (!data.ok) return
            const select = document.getElementById('select-servicio')
            select.innerHTML = '<option value="">Selecciona un servicio</option>' +
                data.data.map(s => `<option value="${s.id}">${s.nombre} (${parseFloat(s.precio_base).toFixed(2)} €)</option>`).join('')
        })
}

function abrirModalReserva(btn) {
    const form = document.getElementById('form-reserva')
    form.querySelector('[name="id_usuario"]').value     = btn.dataset.idUsuario
    form.querySelector('[name="id_solicitud"]').value   = btn.dataset.idSolicitud
    form.querySelector('[name="fecha_evento"]').value   = btn.dataset.fechaEvento
    form.querySelector('[name="num_asistentes"]').value = btn.dataset.participantes
    form.querySelector('[name="observaciones"]').value  = btn.dataset.observaciones ?? ''
    if (!modalReserva) modalReserva = new window.bootstrap.Modal(document.getElementById('modalReserva'))
    modalReserva.show()
}

function cambiarEstado(id, estado, selectEl) {
    if (estado === 'presupuestada') {
        selectPendiente = selectEl
        selectEl.dataset.estadoAnterior = selectEl.dataset.estadoAnterior || [...selectEl.options].find(o => o.defaultSelected)?.value || 'pendiente'
        const form = document.getElementById('form-presupuesto')
        form.querySelector('[name="id"]').value      = id
        form.querySelector('[name="importe"]').value = ''
        const modal = new window.bootstrap.Modal(document.getElementById('modalPresupuesto'))
        modal.show()
        return
    }

    const fd = new FormData()
    fd.append('action', 'cambiarEstado')
    fd.append('id', id)
    fd.append('estado', estado)
    selectEl.disabled = true

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) selectEl.className = `select-estado badge-estado badge-${estado}`
            else window.mostrarToast(data.error)
        })
        .finally(() => { selectEl.disabled = false })
}

function renderTabla(items) {
    const filas = items.map(s => `
        <tr>
            <td class="text-muted">#${s.id}</td>
            <td>
                <div class="fw-medium">${s.cliente} ${s.apellidos}</div>
                <div class="text-muted small">${s.email}</div>
            </td>
            <td>${s.tipo}</td>
            <td>
                ${s.nombre_protagonista ?? '—'}
                ${s.motivo_revision ? `<div class="text-info small mt-1"><i class="bi bi-arrow-repeat"></i> ${s.motivo_revision}</div>` : ''}
            </td>
            <td>${s.fecha_evento}</td>
            <td>${s.num_participantes}</td>
            <td class="text-center">
                ${s.sala ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle text-muted"></i>'}
            </td>
            <td class="text-center">
                ${s.realidad_virtual ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle text-muted"></i>'}
            </td>
            <td class="text-center">
                ${s.tarta ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle text-muted"></i>'}
            </td>
            <td>
                <select class="select-estado badge-estado badge-${s.estado}" data-id="${s.id}" ${s.estado === 'reservada' ? 'disabled' : ''}>
                    ${opcionesEstado(s.estado)}
                </select>
            </td>
            <td>
                ${s.estado === 'aceptada' ? `
                    <button class="btn btn-sm btn-success btn-crear-reserva"
                        data-id-solicitud="${s.id}"
                        data-id-usuario="${s.id_usuario ?? ''}"
                        data-fecha-evento="${s.fecha_evento}"
                        data-participantes="${s.num_participantes}"
                        data-observaciones="${s.observaciones ?? ''}">
                        <i class="bi bi-calendar-plus"></i> Crear reserva
                    </button>` : ''}
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
                        <th>Tipo</th>
                        <th>Protagonista</th>
                        <th>Fecha evento</th>
                        <th>Participantes</th>
                        <th title="Sala">Sala</th>
                        <th title="Realidad Virtual">VR</th>
                        <th title="Tarta">Tarta</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}

function colorEstado(estado) {
    return { pendiente: 'warning', presupuestada: 'info', aceptada: 'success', rechazada: 'danger' }[estado] || ''
}

function opcionesEstado(estado) {
    const transiciones = {
        pendiente:     ['pendiente', 'presupuestada'],
        presupuestada: ['presupuestada', 'pendiente'],
        aceptada:      ['aceptada', 'rechazada'],
        rechazada:     ['rechazada', 'pendiente'],
        reservada:     ['reservada'],
    }
    const nombres = {
        pendiente: 'Pendiente', presupuestada: 'Presupuestada',
        aceptada: 'Aceptada', rechazada: 'Rechazada', reservada: 'Reservada'
    }
    return (transiciones[estado] ?? [estado]).map(e =>
        `<option value="${e}" ${e === estado ? 'selected' : ''}>${nombres[e]}</option>`
    ).join('')
}
