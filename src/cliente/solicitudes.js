const contenedor = document.getElementById('lista-solicitudes')
if (!contenedor) throw new Error('No se encontró #lista-solicitudes')

fetch('/cwu/Controladores/SolicitudControlador.php?action=mostrar')
    .then(res => res.json())
    .then(data => {
        if (!data.ok || !data.data.length) {
            contenedor.innerHTML = '<p class="text-muted">Aún no tienes solicitudes. <a href="/cwu/Vistas/cliente/SolEventoView.php">¡Crea una!</a></p>'
            return
        }
        contenedor.innerHTML = renderTabla(data.data)
    })
    .catch(() => {
        contenedor.innerHTML = '<p class="text-danger">Error al cargar las solicitudes.</p>'
    })

const SALAS = { 1: 'Clínica', 2: 'Librería', 3: 'Ambas' }

function renderTabla(solicitudes) {
    const filas = solicitudes.map(s => `
        <tr>
            <td>${s.fecha_solicitud}</td>
            <td>${s.tipo}</td>
            <td>${s.fecha_evento}</td>
            <td>${s.num_participantes}</td>
            <td>${SALAS[s.sala] ?? '-'}</td>
            <td><span class="badge bg-warning text-dark">Pendiente</span></td>
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
                        <th>Sala</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>
    `
}
