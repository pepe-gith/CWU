const CONTROLADOR = '/cwu/Controladores/ServicioControlador.php';
let eliminarPendiente = null;
let categorias = [];

cargar();

document.getElementById('btn-nuevo-servicio').addEventListener('click', () => {
    abrirModal(null);
});

document.getElementById('form-servicio').addEventListener('submit', e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', fd.get('id') ? 'editar' : 'crear');

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return; }
            window.bootstrap.Modal.getInstance(document.getElementById('modalServicio')).hide();
            window.mostrarToast(data.mensaje, 'success');
            cargar();
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
});

document.getElementById('btn-confirmar-eliminar-servicio').addEventListener('click', () => {
    if (!eliminarPendiente) return;

    const fd = new FormData();
    fd.append('action', 'eliminar');
    fd.append('id', eliminarPendiente);

    window.bootstrap.Modal.getInstance(document.getElementById('modalEliminarServicio')).hide();
    eliminarPendiente = null;

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return; }
            window.mostrarToast(data.mensaje, 'success');
            cargar();
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
});

function cargar() {
    const contenedor = document.getElementById('tabla-servicios');
    contenedor.innerHTML = '<p class="text-muted p-4">Cargando...</p>';

    fetch(`${CONTROLADOR}?action=listar`)
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { contenedor.innerHTML = '<p class="text-danger p-4">Error al cargar.</p>'; return; }

            categorias = data.categorias;
            poblarSelect(categorias);

            if (!data.servicios.length) {
                contenedor.innerHTML = '<p class="text-muted p-4">No hay servicios todavía.</p>';
                return;
            }

            contenedor.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th class="text-end">Precio base</th>
                                <th class="text-center">Capacidad</th>
                                <th class="text-center">Reservas</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.servicios.map(s => `
                                <tr>
                                    <td>
                                        <div class="fw-medium">${s.nombre}</div>
                                        ${s.descripcion ? `<div class="text-muted small">${s.descripcion}</div>` : ''}
                                    </td>
                                    <td><span class="badge bg-secondary">${s.categoria}</span></td>
                                    <td class="text-end">${parseFloat(s.precio_base).toFixed(2)} €</td>
                                    <td class="text-center">${s.capacidad}</td>
                                    <td class="text-center">
                                        ${s.num_reservas > 0
                                            ? `<span class="badge bg-primary">${s.num_reservas}</span>`
                                            : '<span class="text-muted">—</span>'}
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-sm btn-outline-secondary btn-editar-srv"
                                                data-id="${s.id}"
                                                data-nombre="${s.nombre}"
                                                data-descripcion="${s.descripcion ?? ''}"
                                                data-precio="${s.precio_base}"
                                                data-capacidad="${s.capacidad}"
                                                data-categoria="${s.id_categoria}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger btn-eliminar-srv"
                                                data-id="${s.id}" data-nombre="${s.nombre}"
                                                ${s.num_reservas > 0 ? 'disabled title="Tiene reservas asociadas"' : ''}
                                                style="${s.num_reservas > 0 ? 'opacity:.35' : ''}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>`).join('')}
                        </tbody>
                    </table>
                </div>`;

            contenedor.querySelectorAll('.btn-editar-srv').forEach(btn =>
                btn.addEventListener('click', () => abrirModal({
                    id:          btn.dataset.id,
                    nombre:      btn.dataset.nombre,
                    descripcion: btn.dataset.descripcion,
                    precio_base: btn.dataset.precio,
                    capacidad:   btn.dataset.capacidad,
                    id_categoria:btn.dataset.categoria,
                }))
            );

            contenedor.querySelectorAll('.btn-eliminar-srv').forEach(btn =>
                btn.addEventListener('click', () => {
                    eliminarPendiente = btn.dataset.id;
                    document.getElementById('nombre-servicio-eliminar').textContent = btn.dataset.nombre;
                    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEliminarServicio')).show();
                })
            );
        })
        .catch(() => {
            contenedor.innerHTML = '<p class="text-danger p-4">Error al cargar.</p>';
        });
}

function poblarSelect(cats) {
    const sel = document.getElementById('select-categoria');
    sel.innerHTML = '<option value="">Selecciona...</option>' +
        cats.map(c => `<option value="${c.id}">${c.nombre}</option>`).join('');
}

function abrirModal(srv) {
    const form = document.getElementById('form-servicio');
    form.reset();

    if (srv) {
        form.elements['id'].value           = srv.id;
        form.elements['nombre'].value       = srv.nombre;
        form.elements['descripcion'].value  = srv.descripcion;
        form.elements['precio_base'].value  = srv.precio_base;
        form.elements['capacidad'].value    = srv.capacidad;
        form.elements['id_categoria'].value = srv.id_categoria;
        document.getElementById('modal-servicio-titulo').textContent = 'Editar servicio';
    } else {
        form.elements['id'].value = '';
        document.getElementById('modal-servicio-titulo').textContent = 'Nuevo servicio';
    }

    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalServicio')).show();
}
