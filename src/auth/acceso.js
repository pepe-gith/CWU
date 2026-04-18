import { validarNIF } from '../utils/validarNIF'

const form = document.querySelector('#formComprobarAcceso')
if (!form) throw new Error('No se encontró el formulario de acceso')

const errorMsg = document.getElementById('error-msg')

form.addEventListener('submit', function(event) {
    event.preventDefault()
    errorMsg.classList.add('d-none');

    const inputs = form.querySelectorAll('input[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid')
            const feedback = input.nextElementSibling
            if (feedback?.classList.contains('invalid-feedback')) {
                feedback.textContent = 'Campo obligatorio.'
            }
            isValid = false
        } else {
            input.classList.remove('is-invalid')
        }
    });

    const nifInput = form.querySelector('#nif');

    if (nifInput && nifInput.value.trim() && !validarNIF(nifInput.value)) {
        nifInput.classList.add('is-invalid')
        const feedback = nifInput.nextElementSibling
        if (feedback?.classList.contains('invalid-feedback')) {
            feedback.textContent = 'El NIF introducido no es válido.'
        }
        isValid = false
    }

    if (!isValid) return;

    const formData = new FormData(this);
    formData.append('action', 'iniciarSession')

    fetch('/cwu/Controladores/UsuarioControlador.php', {
        method: 'POST',
        body: formData
    })
    .then(async res => {
        const data = await res.json()
        if (!res.ok) throw new Error(data.error ?? data.mensaje)
        return data
    })
    .then(data => {
        location.href = data.redirect
    })
    .catch(error => {
        errorMsg.textContent = error.message || 'Usuario o contraseña incorrectos'
        errorMsg.classList.remove('d-none')
    })
})
