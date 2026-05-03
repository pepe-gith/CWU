const contenedor = document.getElementById('resumen-solicitudes')
if (!contenedor) throw new Error('No se encontró #resumen-solicitudes')

fetch('/cwu/Controladores/SolicitudControlador.php?action=mostrar')
    .then(res => res.json())
    .then(data => {
        if (!data.ok || !data.data.length) {
            contenedor.innerHTML = '<p class="text-muted">Aún no tienes solicitudes. <a href="/cwu/Vistas/cliente/SolEventoView.php">¡Crea una!</a></p>'
            return
        }
        contenedor.innerHTML = renderTabla(data.data.slice(0, 3))
    })
    .catch(() => {
        contenedor.innerHTML = '<p class="text-danger">Error al cargar las solicitudes.</p>'
    })

const BADGES = {
    pendiente:     '<span class="badge bg-warning text-dark">Pendiente</span>',
    presupuestada: '<span class="badge bg-info text-dark">Presupuestada ⚠️</span>',
    aceptada:      '<span class="badge bg-success">Aceptada</span>',
    reservada:     '<span class="badge text-white" style="background:#6f42c1">Reservada</span>',
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
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>
    `
}
