import { validarNIF } from '../utils/validarNIF';

const form = document.querySelector('#formComprobarAcceso');
if (!form) throw new Error('No se encontró el formulario de acceso');

const errorMsg = document.getElementById('error-msg');

function marcarError(input, mensaje) {
    input.classList.add('is-invalid');
    const feedback = input.nextElementSibling;
    if (feedback?.classList.contains('invalid-feedback')) {
        feedback.textContent = mensaje;
    }
}

form.querySelector('#nif')?.addEventListener('blur', function() {
    const valor = this.value.trim();
    if (!valor) return marcarError(this, 'Campo obligatorio.');
    if (!validarNIF(valor)) return marcarError(this, 'El NIF introducido no es válido.');
    this.classList.remove('is-invalid');
});

form.querySelector('#contra')?.addEventListener('blur', function() {
    if (!this.value.trim()) return marcarError(this, 'Campo obligatorio.');
    this.classList.remove('is-invalid');
});

form.addEventListener('submit', function(event) {
    event.preventDefault();
    errorMsg.classList.add('d-none');

    const inputs = form.querySelectorAll('input[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');

            // Muestra el mensaje de error específico para campos vacíos
            const feedback = input.nextElementSibling;
            if (feedback?.classList.contains('invalid-feedback')) {
                feedback.textContent = 'Campo obligatorio.';
            }
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    const nifInput = form.querySelector('#nif');

    // Validación específica para el campo NIF
    if (nifInput && nifInput.value.trim() && !validarNIF(nifInput.value)) {

        nifInput.classList.add('is-invalid');

        // Muestra el mensaje de error específico para NIF no válido
        const feedback = nifInput.nextElementSibling;
        if (feedback?.classList.contains('invalid-feedback')) {
            feedback.textContent = 'El NIF introducido no es válido.';
        }
        isValid = false;
    }

    if (!isValid) return;

    const formData = new FormData(this);
    formData.append('action', 'iniciarSession');

    fetch('/cwu/Controladores/UsuarioControlador.php', {
        method: 'POST',
        body: formData
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.error ?? data.mensaje);
        return data;
    })
    .then(data => {
        location.href = data.redirect;
    })
    .catch(error => {
        errorMsg.textContent = error.message || 'Usuario o contraseña incorrectos';
        errorMsg.classList.remove('d-none');
    })
})
