const CONTROLADOR       = '/cwu/Controladores/AdminControlador.php';
const PAGO_CONTROLADOR  = '/cwu/Controladores/PagoControlador.php';
let servicios = []

fetch(`${CONTROLADOR}?action=obtenerServicios`)
    .then(r => r.json())
    .then(data => { if (data.ok) servicios = data.data });
let filtroFecha  = 'proximas';
let filtroEstado = '';
let filtroCliente= '';
let buscarTimer  = null;

cargarReservas();

// Filtro fecha
document.querySelectorAll('.filtro-fecha').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filtro-fecha').forEach(b => {
            b.classList.remove('active', 'btn-primary');
            b.classList.add('btn-outline-secondary');
        });
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('active', 'btn-primary');
        filtroFecha = btn.dataset.filtro;
        cargarReservas();
    });
});

// Filtro estado
document.getElementById('filtro-estado').addEventListener('change', e => {
    filtroEstado = e.target.value;
    cargarReservas();
});

// Búsqueda cliente con debounce
document.getElementById('filtro-cliente').addEventListener('input', e => {
    clearTimeout(buscarTimer);
    buscarTimer = setTimeout(() => {
        filtroCliente = e.target.value.trim();
        cargarReservas();
    }, 300);
});

// Limpiar filtros
document.getElementById('btn-limpiar').addEventListener('click', () => {
    filtroFecha   = 'proximas';
    filtroEstado  = '';
    filtroCliente = '';
    document.getElementById('filtro-estado').value  = '';
    document.getElementById('filtro-cliente').value = '';
    document.querySelectorAll('.filtro-fecha').forEach(b => {
        b.classList.remove('active', 'btn-primary');
        b.classList.add('btn-outline-secondary');
    });
    document.querySelector('.filtro-fecha[data-filtro="proximas"]').classList.add('active', 'btn-primary');
    document.querySelector('.filtro-fecha[data-filtro="proximas"]').classList.remove('btn-outline-secondary');
    cargarReservas();
});

function cargarReservas() {
    const params = new URLSearchParams({
        action: 'reservas',
        filtro: filtroFecha,
        ...(filtroEstado  && { estado:  filtroEstado }),
        ...(filtroCliente && { cliente: filtroCliente }),
    });

    document.getElementById('tabla-reservas').innerHTML = '<p class="text-muted p-4">Cargando...</p>';

    fetch(`${CONTROLADOR}?${params}`)
        .then(r => r.json())
        .then(data => {
            const contenedor = document.getElementById('tabla-reservas');
            contenedor.innerHTML = data.data.length
                ? renderTabla(data.data)
                : '<p class="text-muted p-4">No hay reservas con esos filtros.</p>';

            contenedor.querySelectorAll('.select-estado').forEach(sel =>
                sel.addEventListener('change', () => cambiarEstado(sel.dataset.id, sel.value, sel))
            );
            contenedor.querySelectorAll('.btn-gestionar-cambio').forEach(btn =>
                btn.addEventListener('click', () => gestionarCambio(btn.dataset.id))
            );
            contenedor.querySelectorAll('.btn-editar-reserva').forEach(btn =>
                btn.addEventListener('click', () => abrirEditar(JSON.parse(btn.dataset.r)))
            );
            contenedor.querySelectorAll('.btn-empleados-reserva').forEach(btn =>
                btn.addEventListener('click', () => abrirEmpleados(btn.dataset.id, btn.dataset.label))
            );
            contenedor.querySelectorAll('.btn-pagos-reserva').forEach(btn =>
                btn.addEventListener('click', () => abrirPagos(btn.dataset.id, btn.dataset.label))
            );
        })
        .catch(() => {
            document.getElementById('tabla-reservas').innerHTML = '<p class="text-danger p-4">Error al cargar.</p>';
        });
}

let cancelPendiente = null;

function cambiarEstado(id, estado, selectEl) {
    if (estado === 'cancelada') {
        cancelPendiente = { id, selectEl }
        document.getElementById('admin-motivo-cancelacion').value = '';
        window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalMotivoCancelacion')).show();
        return;
    }
    enviarCambioEstado(id, estado, null, selectEl);
}

function enviarCambioEstado(id, estado, motivo, selectEl) {
    const fd = new FormData();
    fd.append('action', 'cambiarEstadoReserva');
    fd.append('id', id);
    fd.append('estado', estado);
    if (motivo) fd.append('motivo', motivo);
    selectEl.disabled = true;

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) cargarReservas();
            else { window.mostrarToast(data.error, 'danger'); selectEl.value = selectEl.dataset.estadoAnterior }
        })
        .finally(() => { selectEl.disabled = false });
}

document.getElementById('btn-confirmar-cancelacion-admin').addEventListener('click', () => {
    if (!cancelPendiente) return;
    const motivo = document.getElementById('admin-motivo-cancelacion').value.trim();
    if (!motivo) { window.mostrarToast('El motivo es obligatorio', 'warning'); return }

    window.bootstrap.Modal.getInstance(document.getElementById('modalMotivoCancelacion')).hide();
    enviarCambioEstado(cancelPendiente.id, 'cancelada', motivo, cancelPendiente.selectEl);
    cancelPendiente = null;
});

document.getElementById('modalMotivoCancelacion').addEventListener('hidden.bs.modal', () => {
    if (cancelPendiente) {
        cancelPendiente.selectEl.value = cancelPendiente.selectEl.dataset.estadoAnterior ?? 'pendiente';
        cancelPendiente = null;
    }
});

function opcionesEstadoReserva(estado) {
    const transiciones = {
        pendiente:  ['pendiente', 'confirmada', 'cancelada'],
        confirmada: ['confirmada', 'cancelada'],
        cancelada:  ['cancelada', 'pendiente'],
    }
    const nombres = { pendiente: 'Pendiente', confirmada: 'Confirmada', cancelada: 'Cancelada' }
    return (transiciones[estado] ?? [estado]).map(e =>
        `<option value="${e}" ${e === estado ? 'selected' : ''}>${nombres[e]}</option>`
    ).join('');
}

function abrirEditar(r) {
    const form = document.getElementById('form-editar-reserva');
    form.elements['id'].value            = r.id;
    form.elements['fecha_evento'].value  = r.fecha_evento;
    form.elements['hora_inicio'].value   = r.hora_inicio.slice(0, 5);
    form.elements['hora_fin'].value      = r.hora_fin.slice(0, 5);
    form.elements['num_asistentes'].value = r.num_asistentes;
    form.elements['observaciones'].value = r.observaciones ?? '';

    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarReserva')).show();
}

document.getElementById('form-editar-reserva').addEventListener('submit', e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'editarReserva');

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.bootstrap.Modal.getInstance(document.getElementById('modalEditarReserva')).hide();
            window.mostrarToast(data.mensaje, 'success');
            cargarReservas();
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
});

function gestionarCambio(id) {
    const fd = new FormData();
    fd.append('action', 'gestionarCambio');
    fd.append('id', id);

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast(data.mensaje, 'success');
            cargarReservas();
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
}

function badgeEmpleados(r) {
    const total = parseInt(r.num_empleados);
    if (!total) return '';
    const aceptadas  = parseInt(r.emp_aceptadas);
    const pendientes = parseInt(r.emp_pendientes);
    const rechazadas = parseInt(r.emp_rechazadas);
    if (rechazadas > 0)
        return ` <span class="badge bg-danger ms-1" title="${rechazadas} rechazada(s)">${aceptadas}/${total} <i class="bi bi-x-lg"></i></span>`
    if (pendientes > 0)
        return ` <span class="badge bg-warning text-dark ms-1" title="${pendientes} pendiente(s)">${aceptadas}/${total} <i class="bi bi-clock"></i></span>`
    return ` <span class="badge bg-success ms-1" title="Todas aceptadas">${total}/${total} <i class="bi bi-check-lg"></i></span>`
}

function renderTabla(items) {
    const filas = items.map(r => `
        <tr>
            <td class="text-muted">#${r.id}</td>
            <td>
                <div class="fw-medium">${r.cliente} ${r.apellidos}</div>
                <div class="text-muted small">${r.telefono ?? ''}</div>
            </td>
            <td>${r.servicio}</td>
            <td>${r.fecha_evento}</td>
            <td>${r.hora_inicio.slice(0,5)} – ${r.hora_fin.slice(0,5)}</td>
            <td>${r.num_asistentes}</td>
            <td>${r.observaciones ? `<span class="text-muted small">${r.observaciones}</span>` : '—'}</td>
            <td>${r.motivo_cancelacion ? `<span class="text-danger small"><i class="bi bi-x-circle"></i> ${r.motivo_cancelacion}</span>` : '—'}</td>
            <td>${r.cambio_solicitado == 1 ? `
                    <div class="mt-1">
                        <span class="badge bg-warning text-dark">Cambio solicitado</span>
                        <div class="text-muted small">${r.motivo_cambio}</div>
                        <button class="btn btn-xs btn-outline-secondary btn-gestionar-cambio mt-1" data-id="${r.id}" style="font-size:.75rem;padding:.1rem .4rem">
                            Marcar como gestionado;
                        </button>
                    </div>` : ''}
            </td>
            <td>
                <select class="select-estado badge-estado badge-reserva-${r.estado}" data-id="${r.id}">
                    ${opcionesEstadoReserva(r.estado)}
                </select>
            </td>
            <td class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-secondary btn-editar-reserva";
                    data-r='${JSON.stringify(r).replace(/'/g, "&#39;")}'>
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-primary btn-empleados-reserva";
                    data-id="${r.id}" data-label="Reserva #${r.id} — ${r.cliente} ${r.apellidos}">
                    <i class="bi bi-people"></i>${badgeEmpleados(r)}
                </button>
                <button class="btn btn-sm btn-outline-success btn-pagos-reserva";
                    data-id="${r.id}" data-label="Reserva #${r.id} — ${r.cliente} ${r.apellidos}";
                    title="Pagos">
                    <i class="bi bi-cash"></i>
                </button>
            </td>
        </tr>
    `).join('');

    return `
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>Fecha evento</th>
                        <th>Horario</th>
                        <th>Asistentes</th>
                        <th>Observaciones</th>
                        <th>Motivo cancelación</th>
                        <th>Cambio solicitado</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}

// --- Asignación de empleados ---

function abrirEmpleados(idReserva, label) {
    document.getElementById('modal-empleados-titulo').textContent = label;
    document.getElementById('form-asignar-empleado').elements['id_reserva'].value = idReserva;
    document.getElementById('form-asignar-empleado').reset();
    document.getElementById('form-asignar-empleado').elements['id_reserva'].value = idReserva;

    cargarAsignaciones(idReserva);
    cargarEmpleadosSelect(idReserva);

    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEmpleadosReserva')).show();
}

function cargarAsignaciones(idReserva) {
    const contenedor = document.getElementById('lista-asignaciones');
    contenedor.innerHTML = '<p class="text-muted">Cargando...</p>';

    fetch(`${CONTROLADOR}?action=asignacionesReserva&id=${idReserva}`)
        .then(r => r.json())
        .then(data => {
            if (!data.data.length) {
                contenedor.innerHTML = '<p class="text-muted">Sin empleados asignados.</p>';
                return;
            }
            contenedor.innerHTML = `
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Empleado</th><th>Rol</th><th>Estado</th><th></th></tr>
                    </thead>
                    <tbody>
                        ${data.data.map(a => `
                            <tr>
                                <td>${a.nombre} ${a.apellidos}</td>
                                <td>${a.rol_evento ?? '—'}</td>
                                <td>${{ pendiente: '<span class="badge bg-warning text-dark">Pendiente</span>', aceptada: '<span class="badge bg-success">Aceptada</span>', rechazada: '<span class="badge bg-danger">Rechazada</span>' }[a.estado] ?? ''}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger btn-eliminar-asignacion" data-id="${a.id}" data-reserva="${idReserva}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>`).join('')}
                    </tbody>
                </table>`

            contenedor.querySelectorAll('.btn-eliminar-asignacion').forEach(btn =>
                btn.addEventListener('click', () => eliminarAsignacion(btn.dataset.id, btn.dataset.reserva))
            );
        });
}

function actualizarBadgeReserva(idReserva) {
    fetch(`${CONTROLADOR}?action=asignacionesReserva&id=${idReserva}`)
        .then(r => r.json())
        .then(data => {
            const btn = document.querySelector(`.btn-empleados-reserva[data-id="${idReserva}"]`);
            if (!btn) return;
            const badge = btn.querySelector('.badge');
            const count = data.data?.length ?? 0;
            if (count > 0) {
                if (badge) badge.textContent = count;
                else btn.insertAdjacentHTML('beforeend', ` <span class="badge bg-primary ms-1">${count}</span>`);
            } else {
                badge?.remove();
            }
        });
}

function cargarEmpleadosSelect(idReserva) {
    const sel = document.getElementById('select-empleado-reserva');
    sel.innerHTML = '<option value="">Cargando...</option>';
    fetch(`${CONTROLADOR}?action=empleados&excluir_reserva=${idReserva}`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">Selecciona un empleado...</option>' +
                (data.data ?? []).map(e => `<option value="${e.id}">${e.nombre} ${e.apellidos}${e.especialidad ? ' — ' + e.especialidad : ''}</option>`).join('');
        });
}

function eliminarAsignacion(id, idReserva) {
    const fd = new FormData();
    fd.append('action', 'eliminarAsignacion');
    fd.append('id', id);

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast('Asignación eliminada', 'success');
            cargarAsignaciones(idReserva);
            cargarEmpleadosSelect(idReserva);
            actualizarBadgeReserva(idReserva);
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
}

document.getElementById('form-asignar-empleado').addEventListener('submit', e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'asignarEmpleado');

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast('Empleado asignado', 'success');
            e.target.reset();
            e.target.elements['id_reserva'].value = fd.get('id_reserva');
            cargarAsignaciones(fd.get('id_reserva'));
            cargarEmpleadosSelect(fd.get('id_reserva'));
            actualizarBadgeReserva(fd.get('id_reserva'));
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
});

// --- Pagos ---

function abrirPagos(idReserva, label) {
    document.getElementById('modal-pagos-titulo').textContent = label;
    document.getElementById('form-registrar-pago').reset();
    document.getElementById('form-registrar-pago').elements['id_reserva'].value = idReserva;
    cargarPagos(idReserva);
    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPagosReserva')).show();
}

function cargarPagos(idReserva) {
    const lista  = document.getElementById('lista-pagos');
    const total  = document.getElementById('total-pagado');
    lista.innerHTML = '<p class="text-muted">Cargando...</p>';

    fetch(`${PAGO_CONTROLADOR}?action=obtener&id_reserva=${idReserva}`)
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { lista.innerHTML = '<p class="text-danger">Error al cargar.</p>'; return }

            total.textContent = `Total confirmado: ${parseFloat(data.total).toFixed(2)} €`

            if (!data.pagos.length) {
                lista.innerHTML = '<p class="text-muted">Sin pagos registrados.</p>';
                return;
            }

            const metodoLabel = { tarjeta: 'Tarjeta', efectivo: 'Efectivo', transferencia: 'Transferencia', bizum: 'Bizum' }
            const estadoBadge = {
                pendiente:  '<span class="badge bg-warning text-dark">Pendiente</span>',
                confirmado: '<span class="badge bg-success">Confirmado</span>',
                rechazado:  '<span class="badge bg-danger">Rechazado</span>',
            }

            lista.innerHTML = `
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Fecha</th><th>Monto</th><th>Método</th><th>Referencia</th><th>Estado</th><th></th></tr>
                    </thead>
                    <tbody>
                        ${data.pagos.map(p => `
                            <tr>
                                <td>${p.fecha}</td>
                                <td>${parseFloat(p.monto).toFixed(2)} €</td>
                                <td>${metodoLabel[p.metodo] ?? p.metodo}</td>
                                <td>${p.referencia ? `<span class="text-muted small">${p.referencia}</span>` : '—'}</td>
                                <td>${estadoBadge[p.estado] ?? p.estado}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                    ${p.estado === 'pendiente' ? `
                                        <button class="btn btn-sm btn-success btn-confirmar-pago" data-id="${p.id}" data-reserva="${idReserva}" title="Confirmar">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-rechazar-pago" data-id="${p.id}" data-reserva="${idReserva}" title="Rechazar">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    ` : `
                                        <button class="btn btn-sm btn-outline-danger btn-eliminar-pago" data-id="${p.id}" data-reserva="${idReserva}" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    `}
                                    </div>
                                </td>
                            </tr>`).join('')}
                    </tbody>
                </table>`

            lista.querySelectorAll('.btn-eliminar-pago').forEach(btn =>
                btn.addEventListener('click', () => eliminarPago(btn.dataset.id, btn.dataset.reserva))
            );
            lista.querySelectorAll('.btn-confirmar-pago').forEach(btn =>
                btn.addEventListener('click', () => gestionarPago('confirmar', btn.dataset.id, btn.dataset.reserva))
            );
            lista.querySelectorAll('.btn-rechazar-pago').forEach(btn =>
                btn.addEventListener('click', () => gestionarPago('rechazar', btn.dataset.id, btn.dataset.reserva))
            );
        })
        .catch(() => { lista.innerHTML = '<p class="text-danger">Error al cargar.</p>' });
}

function gestionarPago(accion, id, idReserva) {
    const fd = new FormData();
    fd.append('action', accion);
    fd.append('id', id);

    fetch(PAGO_CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast(data.mensaje, 'success');
            cargarPagos(idReserva);
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
}

function eliminarPago(id, idReserva) {
    const fd = new FormData();
    fd.append('action', 'eliminar');
    fd.append('id', id);

    fetch(PAGO_CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast('Pago eliminado', 'success');
            cargarPagos(idReserva);
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
}

document.getElementById('form-registrar-pago').addEventListener('submit', e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'crear');

    fetch(PAGO_CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { window.mostrarToast(data.error, 'danger'); return }
            window.mostrarToast(data.mensaje, 'success');
            e.target.reset();
            e.target.elements['id_reserva'].value = fd.get('id_reserva');
            cargarPagos(fd.get('id_reserva'));
        })
        .catch(() => window.mostrarToast('Error de conexión', 'danger'));
});
