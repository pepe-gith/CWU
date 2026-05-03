const contenedor = document.getElementById('lista-reservas')

fetch('/cwu/Controladores/SolicitudControlador.php?action=misReservas')
    .then(r => r.json())
    .then(data => {
        if (!data.ok || !data.data.length) {
            contenedor.innerHTML = '<p class="text-muted">Aún no tienes reservas.</p>'
            return
        }
        contenedor.innerHTML = renderTabla(data.data)
    })
    .catch(() => {
        contenedor.innerHTML = '<p class="text-danger">Error al cargar las reservas.</p>'
    })

const BADGES = {
    pendiente:  '<span class="badge bg-warning text-dark">Pendiente</span>',
    confirmada: '<span class="badge bg-success">Confirmada</span>',
    cancelada:  '<span class="badge bg-danger">Cancelada</span>',
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
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}
