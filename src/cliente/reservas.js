const contenedor = document.getElementById('lista-reservas')

function cargar() {
    fetch('/cwu/Controladores/SolicitudControlador.php?action=misReservas')
        .then(r => r.json())
        .then(data => {
            if (!data.ok || !data.data.length) {
                contenedor.innerHTML = '<p class="text-muted">Aún no tienes reservas.</p>'
                return
            }
            contenedor.innerHTML = renderTabla(data.data)
            contenedor.querySelectorAll('.btn-cancelar').forEach(btn =>
                btn.addEventListener('click', () => cancelar(btn.dataset.id))
            )
            contenedor.querySelectorAll('.btn-cambio').forEach(btn =>
                btn.addEventListener('click', () => abrirCambio(btn.dataset.id))
            )
        })
        .catch(() => {
            contenedor.innerHTML = '<p class="text-danger">Error al cargar las reservas.</p>'
        })
}

const BADGES = {
    pendiente:  '<span class="badge bg-warning text-dark">Pendiente</span>',
    confirmada: '<span class="badge bg-success">Confirmada</span>',
    cancelada:  '<span class="badge bg-danger">Cancelada</span>',
}

function puedeCancel(r) {
    if (!['pendiente', 'confirmada'].includes(r.estado)) return false
    const horasRestantes = (new Date(r.fecha_evento) - new Date()) / 36e5
    return horasRestantes >= 24
}

function renderTabla(reservas) {
    const filas = reservas.map(r => `
        <tr>
            <td>${r.fecha_evento}</td>
            <td>${r.hora_inicio.slice(0,5)} – ${r.hora_fin.slice(0,5)}</td>
            <td>${r.servicio}</td>
            <td>${r.num_asistentes}</td>
            <td>${r.observaciones ? `<span class="text-muted small">${r.observaciones}</span>` : '—'}</td>
            <td>${BADGES[r.estado] ?? r.estado}</td>
            <td>${r.estado === 'cancelada' && r.motivo_cancelacion
                    ? `<span class="text-muted small">${r.motivo_cancelacion}</span>`
                    : '—'}</td>
            <td>
                <div class="d-flex gap-1 flex-wrap">
                ${puedeCancel(r)
                ? `<button class="btn btn-sm btn-outline-warning btn-cambio" data-id="${r.id}">Solicitar cambio</button>
                   <button class="btn btn-sm btn-outline-danger btn-cancelar" data-id="${r.id}">Cancelar</button>`
                : r.estado !== 'cancelada'
                ? `<span class="text-muted small">Para cambios llámanos al <strong>600 000 000</strong> o escríbenos a <strong>info@cwu.es</strong></span>`
                : ''
                }
                </div>
            </td>
        </tr>
    `).join('')

    return `
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Servicio</th>
                        <th>Asistentes</th>
                        <th>Observaciones</th>
                        <th>Estado</th>
                        <th>Motivo cancelación</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}

let reservaACancelar = null
let reservaACambiar  = null

function abrirCambio(id) {
    reservaACambiar = id
    document.getElementById('motivo-cambio').value = ''
    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCambio')).show()
}

document.getElementById('btn-confirmar-cambio').addEventListener('click', () => {
    if (!reservaACambiar) return
    const motivo = document.getElementById('motivo-cambio').value.trim()
    if (!motivo) { window.mostrarToast('El motivo es obligatorio.', 'warning'); return }

    const fd = new FormData()
    fd.append('action', 'solicitarCambio')
    fd.append('id', reservaACambiar)
    fd.append('motivo', motivo)

    window.bootstrap.Modal.getInstance(document.getElementById('modalCambio')).hide()

    fetch('/cwu/Controladores/SolicitudControlador.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast(data.mensaje, 'success')
            cargar()
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'))
})

function cancelar(id) {
    reservaACancelar = id
    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCancelar')).show()
}

document.getElementById('btn-confirmar-cancelar').addEventListener('click', () => {
    if (!reservaACancelar) return

    const motivo = document.getElementById('motivo-cancelacion').value.trim()

    const fd = new FormData()
    fd.append('action', 'cancelarReserva')
    fd.append('id', reservaACancelar)
    if (motivo) fd.append('motivo', motivo)

    window.bootstrap.Modal.getInstance(document.getElementById('modalCancelar')).hide()
    document.getElementById('motivo-cancelacion').value = ''

    fetch('/cwu/Controladores/SolicitudControlador.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast(data.mensaje, 'success')
            cargar()
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'))
})

cargar()
