const CONTROLADOR = '/cwu/Controladores/AdminControlador.php'
let estadoActivo = ''

cargarSolicitudes('')

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
        })
        .catch(() => {
            document.getElementById('tabla-solicitudes').innerHTML = '<p class="text-danger p-4">Error al cargar.</p>'
        })
}

function cambiarEstado(id, estado, selectEl) {
    const fd = new FormData()
    fd.append('action', 'cambiarEstado')
    fd.append('id', id)
    fd.append('estado', estado)

    selectEl.disabled = true

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                selectEl.className = `select-estado badge-estado badge-${estado}`
            } else {
                alert(data.error)
            }
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
            <td>${s.nombre_protagonista ? `<span class="text-muted small">${s.nombre_protagonista}</span>` : '—'}</td>
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
                <select class="select-estado badge-estado badge-${s.estado}" data-id="${s.id}">
                    <option value="pendiente"     ${s.estado === 'pendiente'     ? 'selected' : ''}>Pendiente</option>
                    <option value="presupuestada" ${s.estado === 'presupuestada' ? 'selected' : ''}>Presupuestada</option>
                    <option value="aceptada"      ${s.estado === 'aceptada'      ? 'selected' : ''}>Aceptada</option>
                    <option value="rechazada"     ${s.estado === 'rechazada'     ? 'selected' : ''}>Rechazada</option>
                </select>
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
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}

function colorEstado(estado) {
    return { pendiente: 'warning', presupuestada: 'info', aceptada: 'success', rechazada: 'danger' }[estado] || ''
}
