const pathControlador = '/cwu/Controladores/CategoriaControlador.php';
let eliminarPendiente = null;

cargar();

document.getElementById('btn-nueva-categoria').addEventListener('click', () => {
    abrirModal(null);
})

document.getElementById('form-categoria').addEventListener('submit', evento => {
    evento.preventDefault();
    const fd = new FormData(evento.target);
    fd.append('action', fd.get('id') ? 'editar' : 'crear');

    fetch(pathControlador, { method: 'POST', body: fd })
        .then(resultado => resultado.json())
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

    fetch(pathControlador, { method: 'POST', body: fd })
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

    fetch(`${pathControlador}?action=listar`)
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
                                        ${categoria.requiere_sala_vr == 1
                                            ? '<span class="badge bg-info text-dark ms-2">Sala + RV</span>'
                                            : ''}
                                        ${categoria.requiere_tarta == 1
                                            ? '<span class="badge bg-warning text-dark ms-2">Tarta</span>'
                                            : ''}
                                        ${categoria.num_solicitudes > 0
                                            ? `<span class="badge bg-primary ms-2">${categoria.num_solicitudes} ${categoria.num_solicitudes === 1 ? 'solicitud' : 'solicitudes'}</span>`
                                            : ''}
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-sm btn-outline-secondary btn-editar-cat" data-id="${categoria.id}" data-nombre="${categoria.nombre}" data-sala-vr="${categoria.requiere_sala_vr}" data-tarta="${categoria.requiere_tarta}">
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
                btn.addEventListener('click', () => abrirModal({ id: btn.dataset.id, nombre: btn.dataset.nombre, requiere_sala_vr: btn.dataset.salaVr, requiere_tarta: btn.dataset.tarta }))
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
        document.getElementById('chk-sala-vr').checked = cat.requiere_sala_vr == 1;
        document.getElementById('chk-tarta').checked   = cat.requiere_tarta == 1;
        document.getElementById('modal-categoria-titulo').textContent = 'Editar categoría';
    } else {
        form.elements['id'].value = '';
        document.getElementById('chk-sala-vr').checked = false;
        document.getElementById('chk-tarta').checked   = false;
        document.getElementById('modal-categoria-titulo').textContent = 'Nueva categoría';
    }
    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCategoria')).show();
}
