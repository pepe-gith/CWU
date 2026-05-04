const CONTROLADOR = '/cwu/Controladores/CategoriaControlador.php';
let eliminarPendiente = null;

cargar();

document.getElementById('btn-nueva-categoria').addEventListener('click', () => {
    abrirModal(null);
})

document.getElementById('form-categoria').addEventListener('submit', e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', fd.get('id') ? 'editar' : 'crear');

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.bootstrap.Modal.getInstance(document.getElementById('modalCategoria')).hide();
            window.mostrarToast(data.mensaje, 'success');
            cargar();
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
})

document.getElementById('btn-confirmar-eliminar-categoria').addEventListener('click', () => {
    if (!eliminarPendiente) return

    const fd = new FormData();
    fd.append('action', 'eliminar');
    fd.append('id', eliminarPendiente);

    window.bootstrap.Modal.getInstance(document.getElementById('modalEliminarCategoria')).hide();
    eliminarPendiente = null;

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast(data.mensaje, 'success')
            cargar()
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
})

function cargar() {
    const contenedor = document.getElementById('tabla-categorias')
    contenedor.innerHTML = '<p class="text-muted p-4">Cargando...</p>'

    fetch(`${CONTROLADOR}?action=listar`)
        .then(r => r.json())
        .then(data => {
            if (!data.ok || !data.categorias.length) {
                contenedor.innerHTML = '<p class="text-muted p-4">No hay categorías todavía.</p>'
                return;
            }
            contenedor.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.categorias.map(categoria => `
                                <tr>
                                    <td class="text-muted">${categoria.id}</td>
                                    <td>
                                        <span class="fw-medium">${categoria.nombre}</span>
                                        ${categoria.num_solicitudes > 0
                                            ? `<span class="badge bg-primary ms-2">${categoria.num_solicitudes} ${categoria.num_solicitudes === 1 ? 'solicitud' : 'solicitudes'}</span>`
                                            : ''}
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-sm btn-outline-secondary btn-editar-cat" data-id="${categoria.id}" data-nombre="${categoria.nombre}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger btn-eliminar-cat" data-id="${categoria.id}" data-nombre="${categoria.nombre}"
                                                ${categoria.num_solicitudes > 0 ? 'disabled title="Tiene solicitudes asociadas"' : ''} style="${categoria.num_solicitudes > 0 ? 'opacity:.35' : ''}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>`).join('')}
                        </tbody>
                    </table>
                </div>`;

            contenedor.querySelectorAll('.btn-editar-cat').forEach(btn =>
                btn.addEventListener('click', () => abrirModal({ id: btn.dataset.id, nombre: btn.dataset.nombre }))
            );
            contenedor.querySelectorAll('.btn-eliminar-cat').forEach(btn =>
                btn.addEventListener('click', () => {
                    eliminarPendiente = btn.dataset.id;
                    document.getElementById('nombre-categoria-eliminar').textContent = btn.dataset.nombre;
                    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEliminarCategoria')).show();
                })
            );
        })
        .catch(() => {
            document.getElementById('tabla-categorias').innerHTML = '<p class="text-danger p-4">Error al cargar.</p>';
        });
}

function abrirModal(cat) {
    const form = document.getElementById('form-categoria');
    form.reset();
    if (cat) {
        form.elements['id'].value     = cat.id;
        form.elements['nombre'].value = cat.nombre;
        document.getElementById('modal-categoria-titulo').textContent = 'Editar categoría';
    } else {
        form.elements['id'].value = '';
        document.getElementById('modal-categoria-titulo').textContent = 'Nueva categoría';
    }
    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCategoria')).show();
}
