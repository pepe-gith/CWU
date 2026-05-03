fetch('/cwu/Controladores/AdminControlador.php?action=dashboard')
    .then(r => r.json())
    .then(data => {
        if (!data.ok) return
        const d = data.data

        document.getElementById('kpi-solicitudes').textContent = d.solicitudes_pendientes
        document.getElementById('kpi-reservas').textContent    = d.reservas_proximas
        document.getElementById('kpi-clientes').textContent    = d.total_clientes
        document.getElementById('kpi-ingresos').textContent    = d.ingresos_mes.toFixed(2) + ' €'

        document.getElementById('tabla-solicitudes').innerHTML =
            d.ultimas_solicitudes.length
                ? renderSolicitudes(d.ultimas_solicitudes)
                : '<p class="text-muted">No hay solicitudes pendientes.</p>'

        document.getElementById('tabla-reservas').innerHTML =
            d.proximas_reservas.length
                ? renderReservas(d.proximas_reservas)
                : '<p class="text-muted">No hay reservas próximas.</p>'
    })
    .catch(() => {
        document.getElementById('kpis').innerHTML = '<p class="text-danger">Error al cargar los datos.</p>'
    })

function renderSolicitudes(items) {
    const filas = items.map(s => `
        <tr>
            <td>${s.cliente} ${s.apellidos}</td>
            <td>${s.tipo}</td>
            <td>${s.fecha_evento}</td>
            <td>${s.num_participantes}</td>
            <td><span class="badge bg-warning text-dark">Pendiente</span></td>
        </tr>
    `).join('')

    return `
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Fecha evento</th>
                        <th>Participantes</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}

function renderReservas(items) {
    const filas = items.map(r => `
        <tr>
            <td>${r.cliente} ${r.apellidos}</td>
            <td>${r.servicio}</td>
            <td>${r.fecha_evento}</td>
            <td>${r.hora_inicio.slice(0,5)}</td>
        </tr>
    `).join('')

    return `
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}
