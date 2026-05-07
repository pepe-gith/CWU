const CONTROLADOR = '/cwu/Controladores/EmpleadoControlador.php';
const contenedor  = document.getElementById('tabla-agenda');

cargar();

function cargar() {
    contenedor.innerHTML = '<p class="text-muted p-4">Cargando...</p>';
    fetch(`${CONTROLADOR}?action=agenda`)
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { contenedor.innerHTML = '<p class="text-danger p-4">Error al cargar.</p>'; return }
            if (!data.data.length) { contenedor.innerHTML = '<p class="text-muted p-4">No tienes eventos asignados.</p>'; return }
            contenedor.innerHTML = renderTabla(data.data);
            contenedor.querySelectorAll('.btn-responder').forEach(btn =>
                btn.addEventListener('click', () => responder(btn.dataset.id, btn.dataset.estado))
            );
        })
        .catch(() => { contenedor.innerHTML = '<p class="text-danger p-4">Error de conexión.</p>' });
}

function responder(id, estado) {
    const fd = new FormData();
    fd.append('action', 'responderAsignacion');
    fd.append('id', id);
    fd.append('estado', estado);

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast(data.mensaje, 'success');
            cargar();
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
}

const badges = {
    pendiente: '<span class="badge bg-warning text-dark">Pendiente</span>',
    aceptada:  '<span class="badge bg-success">Aceptada</span>',
    rechazada: '<span class="badge bg-danger">Rechazada</span>',
}

function renderTabla(items) {
    const filas = items.map(a => `
        <tr>
            <td>
                <div class="fw-medium">${a.fecha_evento}</div>
                <div class="text-muted small">${a.hora_inicio.slice(0,5)} – ${a.hora_fin.slice(0,5)}</div>
            </td>
            <td>${a.servicio}</td>
            <td>${a.cliente} ${a.cliente_apellidos}</td>
            <td>${a.rol_evento ?? '—'}</td>
            <td>${badges[a.estado] ?? ''}</td>
            <td>
                ${a.estado === 'pendiente' ? `
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-success btn-responder" data-id="${a.id}" data-estado="aceptada">
                            <i class="bi bi-check-lg"></i> Aceptar;
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-responder" data-id="${a.id}" data-estado="rechazada">
                            <i class="bi bi-x-lg"></i> Rechazar;
                        </button>
                    </div>` : '—'}
            </td>
        </tr>
    `).join('');

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
                        <th></th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}
