const pathControlador = '/cwu/Controladores/AdminControlador.php';
const contenedor  = document.getElementById('tabla-empleados');

cargar();

document.getElementById('mostrar-inactivos').addEventListener('change', cargar);

function cargar() {
    const inactivos = document.getElementById('mostrar-inactivos').checked ? '&inactivos=1' : '';
    contenedor.innerHTML = '<p class="text-muted p-4">Cargando...</p>';
    fetch(`${pathControlador}?action=empleados${inactivos}`)
        .then(respuesta => respuesta.json())
        .then(data => {
            if (!data.ok) { contenedor.innerHTML = '<p class="text-danger p-4">Error al cargar.</p>'; return }
            const inactivos = document.getElementById('mostrar-inactivos').checked;
            if (!data.data.length) {
                contenedor.innerHTML = `<p class="text-muted p-4">${inactivos ? 'No hay empleados inactivos.' : 'No hay empleados registrados.'}</p>`
                return;
            }
            contenedor.innerHTML = renderTabla(data.data);
            contenedor.querySelectorAll('.btn-editar').forEach(btn =>
                btn.addEventListener('click', () => abrirEditar(JSON.parse(btn.dataset.e)))
            )
        })
        .catch(() => { contenedor.innerHTML = '<p class="text-danger p-4">Error de conexión.</p>' });
}

function renderTabla(empleados) {
    const filas = empleados.map(e => `
        <tr>
            <td>
                <div class="fw-medium">${e.nombre} ${e.apellidos} ${e.activo == 0 ? '<span class="badge bg-secondary ms-1">Inactivo</span>' : ''}</div>
                <div class="text-muted small">${e.email}</div>
            </td>
            <td>${e.telefono ?? '—'}</td>
            <td>${e.especialidad ?? '—'}</td>
            <td>${parseFloat(e.precio_por_hora).toFixed(2)} €/h</td>
            <td>
                <button class="btn btn-sm btn-outline-primary btn-editar"
                    data-e='${JSON.stringify(e).replace(/'/g, "&#39;")}'>
                    <i class="bi bi-pencil"></i> Editar
                </button>
            </td>
        </tr>
    `).join('');

    return `
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Empleado</th>
                        <th>Teléfono</th>
                        <th>Especialidad</th>
                        <th>Precio/hora</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}

function abrirEditar(e) {
    document.getElementById('modal-empleado-titulo').textContent = `${e.nombre} ${e.apellidos}`
    const form = document.getElementById('form-empleado');
    form.elements['id'].value              = e.id;
    form.elements['especialidad'].value    = e.especialidad ?? '';
    form.elements['precio_por_hora'].value = e.precio_por_hora;

    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEmpleado')).show();
}

document.getElementById('form-empleado').addEventListener('submit', e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'guardarEmpleado');

    fetch(pathControlador, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.bootstrap.Modal.getInstance(document.getElementById('modalEmpleado')).hide();
            window.mostrarToast(data.mensaje, 'success');
            cargar();
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
});
