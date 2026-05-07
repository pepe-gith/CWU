import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import esLocale from '@fullcalendar/core/locales/es';

const pathControlador = '/cwu/Controladores/AdminControlador.php';

const calendar = new Calendar(document.getElementById('calendario'), {
    plugins: [dayGridPlugin],
    locale: esLocale,
    initialView: 'dayGridMonth',
    headerToolbar: {
        left:   'prev,next today',
        center: 'title',
        right:  ''
    },
    height: 'auto',
    events: function (info, successCallback, failureCallback) {
        fetch(`${pathControlador}?action=eventosCalendario&start=${info.startStr}&end=${info.endStr}`)
            .then(r => r.json())
            .then(data => successCallback(data.ok ? data.data : []))
            .catch(() => failureCallback());
    },
    eventClick: function (info) {
        const e = info.event;
        const p = e.extendedProps;

        document.getElementById('detalle-titulo').textContent = e.title;

        let html = `
            <dl class="row mb-0">
                <dt class="col-5">Cliente</dt>
                <dd class="col-7">${p.cliente}</dd>
                <dt class="col-5">Fecha</dt>
                <dd class="col-7">${e.startStr}</dd>`

        if (p.tipo === 'reserva') {
            const empHtml = p.empleados.length
                ? p.empleados.map(e => `${e.nombre} ${e.apellidos}${e.rol_evento ? ' <span class="text-muted">(' + e.rol_evento + ')</span>' : ''}`).join('<br>')
                : '<span class="text-muted">Sin empleado asignado</span>'

            html += `
                <dt class="col-5">Horario</dt>
                <dd class="col-7">${p.hora_inicio} – ${p.hora_fin}</dd>
                <dt class="col-5">Servicio</dt>
                <dd class="col-7">${p.servicio}</dd>
                <dt class="col-5">Asistentes</dt>
                <dd class="col-7">${p.num_asistentes}</dd>
                <dt class="col-5">Estado</dt>
                <dd class="col-7">${p.estado}</dd>
                <dt class="col-5">Empleados</dt>
                <dd class="col-7">${empHtml}</dd>`
        } else {
            html += `
                <dt class="col-5">Tipo evento</dt>
                <dd class="col-7">${p.tipo_evento}</dd>
                <dt class="col-5">Participantes</dt>
                <dd class="col-7">${p.num_participantes}</dd>
                <dt class="col-5">Estado</dt>
                <dd class="col-7">Pendiente</dd>`
        }

        html += '</dl>';
        document.getElementById('detalle-cuerpo').innerHTML = html;
        window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetalleEvento')).show();
    },
    eventDisplay: 'block',
    displayEventTime: false,
    eventDidMount: function (info) {
        info.el.style.cursor = 'pointer';
    },
});

calendar.render();
