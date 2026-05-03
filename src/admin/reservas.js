const CONTROLADOR = '/cwu/Controladores/AdminControlador.php'
let filtroFecha  = 'proximas'
let filtroEstado = ''
let filtroCliente= ''
let buscarTimer  = null

cargarReservas()

// Filtro fecha
document.querySelectorAll('.filtro-fecha').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filtro-fecha').forEach(b => {
            b.classList.remove('active', 'btn-primary')
            b.classList.add('btn-outline-secondary')
        })
        btn.classList.remove('btn-outline-secondary')
        btn.classList.add('active', 'btn-primary')
        filtroFecha = btn.dataset.filtro
        cargarReservas()
    })
})

// Filtro estado
document.getElementById('filtro-estado').addEventListener('change', e => {
    filtroEstado = e.target.value
    cargarReservas()
})

// Búsqueda cliente con debounce
document.getElementById('filtro-cliente').addEventListener('input', e => {
    clearTimeout(buscarTimer)
    buscarTimer = setTimeout(() => {
        filtroCliente = e.target.value.trim()
        cargarReservas()
    }, 300)
})

// Limpiar filtros
document.getElementById('btn-limpiar').addEventListener('click', () => {
    filtroFecha   = 'proximas'
    filtroEstado  = ''
    filtroCliente = ''
    document.getElementById('filtro-estado').value  = ''
    document.getElementById('filtro-cliente').value = ''
    document.querySelectorAll('.filtro-fecha').forEach(b => {
        b.classList.remove('active', 'btn-primary')
        b.classList.add('btn-outline-secondary')
    })
    document.querySelector('.filtro-fecha[data-filtro="proximas"]').classList.add('active', 'btn-primary')
    document.querySelector('.filtro-fecha[data-filtro="proximas"]').classList.remove('btn-outline-secondary')
    cargarReservas()
})

function cargarReservas() {
    const params = new URLSearchParams({
        action: 'reservas',
        filtro: filtroFecha,
        ...(filtroEstado  && { estado:  filtroEstado }),
        ...(filtroCliente && { cliente: filtroCliente }),
    })

    document.getElementById('tabla-reservas').innerHTML = '<p class="text-muted p-4">Cargando...</p>'

    fetch(`${CONTROLADOR}?${params}`)
        .then(r => r.json())
        .then(data => {
            const contenedor = document.getElementById('tabla-reservas')
            contenedor.innerHTML = data.data.length
                ? renderTabla(data.data)
                : '<p class="text-muted p-4">No hay reservas con esos filtros.</p>'

            contenedor.querySelectorAll('.select-estado').forEach(sel => {
                sel.addEventListener('change', () => cambiarEstado(sel.dataset.id, sel.value, sel))
            })
        })
        .catch(() => {
            document.getElementById('tabla-reservas').innerHTML = '<p class="text-danger p-4">Error al cargar.</p>'
        })
}

function cambiarEstado(id, estado, selectEl) {
    const fd = new FormData()
    fd.append('action', 'cambiarEstadoReserva')
    fd.append('id', id)
    fd.append('estado', estado)
    selectEl.disabled = true

    fetch(CONTROLADOR, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) selectEl.className = `select-estado badge-estado badge-reserva-${estado}`
            else alert(data.error)
        })
        .finally(() => { selectEl.disabled = false })
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
            <td>
                <select class="select-estado badge-estado badge-reserva-${r.estado}" data-id="${r.id}">
                    <option value="pendiente"  ${r.estado === 'pendiente'  ? 'selected' : ''}>Pendiente</option>
                    <option value="confirmada" ${r.estado === 'confirmada' ? 'selected' : ''}>Confirmada</option>
                    <option value="cancelada"  ${r.estado === 'cancelada'  ? 'selected' : ''}>Cancelada</option>
                </select>
            </td>
        </tr>
    `).join('')

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
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>`
}
