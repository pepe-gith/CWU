const CONTROLADOR = '/cwu/Controladores/EmpleadoControlador.php'
const contenedor  = document.getElementById('tabla-historial')

cargar()

function cargar() {
    contenedor.innerHTML = '<p class="text-muted p-4">Cargando...</p>'
    fetch(`${CONTROLADOR}?action=historial`)
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { contenedor.innerHTML = '<p class="text-danger p-4">Error al cargar.</p>'; return }
            if (!data.data.length) { contenedor.innerHTML = '<p class="text-muted p-4">No hay eventos pasados.</p>'; return }
            contenedor.innerHTML = renderTabla(data.data)
        })
        .catch(() => { contenedor.innerHTML = '<p class="text-danger p-4">Error de conexión.</p>' })
}

const badgesAsignacion = {
    pendiente: '<span class="badge bg-warning text-dark">Pendiente</span>',
    aceptada:  '<span class="badge bg-success">Aceptada</span>',
    rechazada: '<span class="badge bg-danger">Rechazada</span>',
}

function estadoBadge(a) {
    if (a.estado_reserva === 'cancelada')
        return '<span class="badge bg-secondary">Reserva cancelada</span>'
    return badgesAsignacion[a.estado] ?? ''
}

function renderTabla(items) {
    const filas = items.map(a => `
        <tr class="${a.estado_reserva === 'cancelada' ? 'table-secondary text-muted' : ''}">
            <td>
                <div class="fw-medium">${a.fecha_evento}</div>
                <div class="text-muted small">${a.hora_inicio.slice(0,5)} – ${a.hora_fin.slice(0,5)}</div>
            </td>
            <td>${a.servicio}</td>
            <td>${a.cliente} ${a.cliente_apellidos}</td>
            <td>${a.rol_evento ?? '—'}</td>
            <td>${estadoBadge(a)}</td>
        </tr>
    `).join('')

    return `
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Servicio</th>
                        <th>Cliente</th>
                        <th>Rol</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}
