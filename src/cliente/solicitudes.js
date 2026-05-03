const CONTROLADOR = '/cwu/Controladores/SolicitudControlador.php'
const contenedor  = document.getElementById('lista-solicitudes')
if (!contenedor) throw new Error('No se encontró #lista-solicitudes')

cargarSolicitudes()

function cargarSolicitudes() {
    fetch(`${CONTROLADOR}?action=mostrar`)
        .then(res => res.json())
        .then(data => {
            if (!data.ok || !data.data.length) {
                contenedor.innerHTML = '<p class="text-muted">Aún no tienes solicitudes. <a href="/cwu/Vistas/cliente/SolEventoView.php">¡Crea una!</a></p>'
                return
            }
            contenedor.innerHTML = renderTabla(data.data)
            contenedor.querySelectorAll('.btn-responder').forEach(btn => {
                btn.addEventListener('click', () => responder(btn.dataset.id, btn.dataset.respuesta, btn))
            })
            contenedor.querySelectorAll('.btn-revision').forEach(btn => {
                btn.addEventListener('click', () => {
                    const motivo = document.getElementById(`motivo-${btn.dataset.id}`)?.value ?? ''
                    solicitarRevision(btn.dataset.id, motivo, btn)
                })
            })
        })
        .catch(() => {
            contenedor.innerHTML = '<p class="text-danger">Error al cargar las solicitudes.</p>'
        })
}

function solicitarRevision(id, motivo, btn) {
    btn.disabled = true
    const fd = new FormData()
    fd.append('action', 'solicitarRevision')
    fd.append('id',     id)
    fd.append('motivo', motivo)

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) cargarSolicitudes()
            else window.mostrarToast(data.error)
        })
        .catch(() => { btn.disabled = false })
}

function responder(id, respuesta, btn) {
    btn.disabled = true
    const fd = new FormData()
    fd.append('action',    'responderPresupuesto')
    fd.append('id',        id)
    fd.append('respuesta', respuesta)

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) cargarSolicitudes()
            else window.mostrarToast(data.error)
        })
        .catch(() => { btn.disabled = false })
}

function vencido(fecha) {
    if (!fecha) return false
    return new Date(fecha) < new Date(new Date().toDateString())
}

const BADGES = {
    pendiente:     '<span class="badge bg-warning text-dark">Pendiente</span>',
    presupuestada: '<span class="badge bg-info text-dark">Presupuestada</span>',
    aceptada:      '<span class="badge bg-success">Aceptada</span>',
    reservada:     '<span class="badge bg-purple text-white" style="background:#6f42c1">Reservada</span>',
    rechazada:     '<span class="badge bg-danger">Rechazada</span>',
}

function renderTabla(solicitudes) {
    const filas = solicitudes.map(s => `
        <tr>
            <td>${s.fecha_solicitud}</td>
            <td>${s.tipo}</td>
            <td>${s.fecha_evento}</td>
            <td>${s.num_participantes}</td>
            <td>${BADGES[s.estado] ?? s.estado}</td>
            <td>
                ${s.estado === 'rechazada' && !vencido(s.fecha_limite_presupuesto) ? `
                    <div class="d-flex flex-column gap-2">
                        <textarea class="form-control form-control-sm" id="motivo-${s.id}" rows="2" placeholder="¿Por qué quieres revisarlo? (opcional)"></textarea>
                        <button class="btn btn-sm btn-outline-primary btn-revision" data-id="${s.id}">
                            <i class="bi bi-arrow-repeat"></i> Solicitar revisión
                        </button>
                    </div>` : ''}
                ${s.estado === 'presupuestada' ? `
                    <div class="mb-1 fw-bold text-success fs-5">
                        ${s.importe_presupuesto ? parseFloat(s.importe_presupuesto).toFixed(2) + ' €' : ''}
                    </div>
                    ${s.notas_presupuesto ? `<div class="text-muted small mb-1">${s.notas_presupuesto}</div>` : ''}
                    ${s.fecha_limite_presupuesto ? `<div class="text-danger small mb-2"><i class="bi bi-clock"></i> Válido hasta: ${s.fecha_limite_presupuesto}</div>` : ''}
                    ${vencido(s.fecha_limite_presupuesto) ? `
                    <div class="text-danger small"><i class="bi bi-x-circle"></i> Presupuesto vencido</div>` : `
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success btn-responder" data-id="${s.id}" data-respuesta="aceptada">
                            <i class="bi bi-check-lg"></i> Aceptar
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-responder" data-id="${s.id}" data-respuesta="rechazada">
                            <i class="bi bi-x-lg"></i> Rechazar
                        </button>
                    </div>`}` : '—'}
            </td>
        </tr>
    `).join('')

    return `
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Solicitada</th>
                        <th>Tipo</th>
                        <th>Fecha evento</th>
                        <th>Participantes</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}
