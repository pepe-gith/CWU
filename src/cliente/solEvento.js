// Cargar categorías dinámicamente
const camposEscape = document.getElementById('campos-escape');

fetch('/cwu/Controladores/CategoriaControlador.php?action=listar')
    .then(ressultado => ressultado.json())
    .then(data => {
        const select = document.getElementById('tipo_evento');
        if (!select || !data.ok) return;
        select.innerHTML = '<option value="" disabled selected>Elige tipo de evento</option>';
        data.categorias.forEach(categoria => {
            const opt = document.createElement('option');
            opt.value = categoria.id;
            opt.textContent = categoria.nombre;
            opt.dataset.salaVr = categoria.requiere_sala_vr;
            opt.dataset.tarta  = categoria.requiere_tarta;
            select.appendChild(opt);
        });
    }
);

function actualizarCamposEscape() {
    const select = document.getElementById('tipo_evento');
    const opcion = select.options[select.selectedIndex];
    const pideSalaVr = opcion && opcion.dataset.salaVr === '1';
    const pideTarta  = opcion && opcion.dataset.tarta === '1';

    camposEscape.classList.toggle('d-none', !pideSalaVr);
    camposEscape.querySelectorAll('select').forEach(sel => {
        if (pideSalaVr) {
            sel.setAttribute('required', '');
        } else {
            sel.removeAttribute('required');
            sel.value = '';
        }
    });

    const camposCumple = document.getElementById('campos-cumple');
    camposCumple.classList.toggle('d-none', !pideTarta);
    const tarta = document.getElementById('tarta');
    if (pideTarta) {
        tarta.setAttribute('required', '');
    } else {
        tarta.removeAttribute('required');
        tarta.value = '';
    }
}

document.getElementById('tipo_evento').addEventListener('change', actualizarCamposEscape);

// Obtenemos el formulario
const form = document.getElementById('form-event');
if (!form) throw new Error('No se encontró el formulario de solicitud de evento.');

const errorMsg = document.getElementById('error-msg');
const successMsg = document.getElementById('success-msg');


// Validación blur — campos obligatorios
const camposObligatorios = form.querySelectorAll('input[required], select[required]');
    camposObligatorios.forEach( campo => {

        campo.addEventListener('blur', function() {

            if (!this.value.trim()) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (!this.classList.contains('is-invalid') || this.value.trim()) {
                this.classList.remove('is-invalid');
            }

        });

    }
);

// Calendario
const cal = document.getElementById('cal');
if (cal) {
    flatpickr(cal, {
        locale: 'es',
        minDate: 'today',
        dateFormat: 'Y-m-d',
        inline: true,
        disable: [],
        onChange: function(_selectedDates, dateStr) {
            document.getElementById('fecha_evento').value = dateStr;
        }
    });
}



form.addEventListener('submit', function(event) {
    event.preventDefault();
    errorMsg.classList.add('d-none');
    successMsg.classList.add('d-none');

    let hayErrores = false;

    camposObligatorios.forEach(campo => {
        if (!campo.value.trim()) {
            campo.classList.add('is-invalid');
            hayErrores = true;
        }
    });

    if (!document.getElementById('fecha_evento').value) {
        hayErrores = true;
    }

    if (hayErrores) {
        errorMsg.textContent = 'Corrige los errores antes de continuar.';
        errorMsg.classList.remove('d-none');
        return;
    }

    const formData = new FormData(this);
    formData.append('action', 'crear');

    fetch('/cwu/Controladores/SolicitudControlador.php', {
        method: 'POST',
        body: formData
    })
    .then(async resultado => {
        const data = await resultado.json();
        if (!resultado.ok) throw new Error(data.error ?? 'Error al enviar la solicitud.');
        return data;
    })
    .then(() => {
        successMsg.textContent = 'Solicitud enviada correctamente. Nos pondremos en contacto contigo.';
        successMsg.classList.remove('d-none');
        form.reset();
    })
    .catch(error => {
        errorMsg.textContent = error.message;
        errorMsg.classList.remove('d-none');
    });
});

