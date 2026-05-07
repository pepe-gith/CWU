import { validarNIF } from '../utils/validarNIF';

const form = document.querySelector('#formRegistro');
if (!form) throw new Error('RegistroView not found');

const errorMsg  = document.getElementById('error-msg');
const successMsg = document.getElementById('success-msg');

// para no hacer fetch en cada tecla
function debounce(funcion, espera) {
    let temporizador;
    return function() {
        clearTimeout(temporizador);
        temporizador = setTimeout(funcion, espera);
    };
}

function verificarCampo(campo, valor, input) {
    if (!valor) return;

    const formData = new FormData();
    formData.append('campo', campo);
    formData.append('valor', valor);

    formData.append('action', 'verificarCampo');
    fetch('/cwu/Controladores/UsuarioControlador.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            input.classList.toggle('is-invalid', data.existe)
            input.classList.remove('is-valid')
            const feedback = input.nextElementSibling
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = data.existe
                    ? `Este ${campo === 'nif' ? 'NIF' : 'email'} ya está registrado.`
                    : ''
            }
        })
}

// Validación blur — campos obligatorios
const camposObligatorios = form.querySelectorAll('input[required]');
camposObligatorios.forEach(input => {
    input.addEventListener('blur', function() {
        if (!this.value.trim()) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else if (!this.classList.contains('is-invalid') || this.value.trim()) {
            this.classList.remove('is-invalid');
        }
    })
})

// Validación en tiempo real — NIF y email
const nifInput   = document.getElementById('nif');
const emailInput = document.getElementById('email1');

nifInput.addEventListener('input', debounce(
    () => verificarCampo('nif', nifInput.value, nifInput), 500
));
nifInput.addEventListener('blur', function() {
    const feedback = this.nextElementSibling;
    if (!this.value.trim()) {
        this.classList.add('is-invalid');
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = 'Campo obligatorio.';
        }
    } else if (!validarNIF(this.value)) {
        this.classList.add('is-invalid');
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = 'El NIF introducido no es válido.';
        }
    }
})

emailInput.addEventListener('input', debounce(
    () => verificarCampo('email', emailInput.value, emailInput), 500
));
emailInput.addEventListener('blur', function() {
    if (!this.value.trim()) {
        this.classList.add('is-invalid');
        const feedback = this.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = 'Campo obligatorio.';
        }
    }
})

// Validación visual de contraseña en tiempo real
const password = document.getElementById('password');
password.addEventListener('input', function() {
    const valido = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/.test(this.value);
    const escrito = this.value.length > 0;
    this.classList.toggle('is-valid', valido);
    this.classList.toggle('is-invalid', !valido && escrito);
    // Oculta el texto de ayuda cuando la contraseña ya es válida
    const ayuda = this.nextElementSibling;
    if (ayuda && ayuda.classList.contains('form-text')) {
        ayuda.classList.toggle('d-none', valido);
    }
})

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

    if (nifInput && !validarNIF(nifInput.value)) {
        nifInput.classList.add('is-invalid');
        const feedback = nifInput.nextElementSibling;
        if (feedback?.classList.contains('invalid-feedback')) {
            feedback.textContent = 'El NIF introducido no es válido.'
        }
        hayErrores = true
    }

    // Bloquear envío si hay campos inválidos
    if (hayErrores) {
        errorMsg.textContent = 'Corrige los errores antes de continuar.';
        errorMsg.classList.remove('d-none');
        return;
    }

    const formData = new FormData(this);

    formData.append('action', 'registrar');
    fetch('/cwu/Controladores/UsuarioControlador.php', { method: 'POST', body: formData })
        .then(async res => {
            const datos = await res.json();
            if (!datos.ok) throw new Error(datos.error || 'Error al registrar el cliente');
            return datos;
        })
        .then(() => {
            successMsg.textContent = 'Cuenta creada correctamente. Redirigiendo...';
            successMsg.classList.remove('d-none');
            form.reset();
            setTimeout(() => location.href = '/cwu/Vistas/auth/AccesoView.php', 2000);
        })
        .catch(err => {
            errorMsg.textContent = err.message;
            errorMsg.classList.remove('d-none');
        })
})
