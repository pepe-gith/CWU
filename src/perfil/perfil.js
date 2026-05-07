const pathControlador = '/cwu/Controladores/UsuarioControlador.php';

// Cargar datos al inicio
fetch(`${pathControlador}?action=obtenerPerfil`)
    .then(r => r.json())
    .then(datos => {
        if (!datos.ok) return mostrarAlerta('danger', datos.error);

        const usuario = datos.data;
        document.getElementById('nombre').value = usuario.nombre ?? '';
        document.getElementById('apellidos').value = usuario.apellidos ?? '';
        document.getElementById('nif').value = usuario.nif ?? '';
        document.getElementById('email').value = usuario.email ?? '';
        document.getElementById('telefono').value = usuario.telefono ?? '';
        document.getElementById('otro_telefono').value = usuario.otro_telefono ?? '';
        document.getElementById('direccion').value = usuario.direccion ?? '';

    })
    .catch(
        function() { 
            mostrarAlerta('danger', 'Error al cargar los datos del perfil'); 
        }
    );

// Guardar datos personales
document.getElementById('form-perfil').addEventListener('submit', async evento => {
    evento.preventDefault();
    const spinner = document.getElementById('spinner-perfil');
    const btn     = document.getElementById('btn-guardar');
    spinner.classList.remove('d-none');
    btn.disabled = true;

    try {
        const body = new FormData(evento.target);
        body.append('action', 'actualizarPerfil');
        const res  = await fetch(pathControlador, { method: 'POST', body });
        const data = await res.json();
        if (data.ok) {
            mostrarAlerta('success', data.mensaje);
        } else  {

            mostrarAlerta('danger',  data.error);
        }   
    } catch {
        mostrarAlerta('danger', 'Error de conexión');
    } finally {
        spinner.classList.add('d-none');
        btn.disabled = false;
    }
});

// Cambiar contraseña
document.getElementById('form-password').addEventListener('submit', async evento => {
    evento.preventDefault();
    const spinner = document.getElementById('spinner-password');
    const btn     = document.getElementById('btn-password');

    const nueva     = evento.target.password_nueva.value;
    const confirmar = evento.target.password_confirmar.value;

    if (nueva !== confirmar) {
        mostrarAlerta('danger', 'Las contraseñas no coinciden');
        return;
    }

    spinner.classList.remove('d-none');
    btn.disabled = true;

    try {
        const body = new FormData(evento.target);
        body.append('action', 'cambiarPassword');

        const respuesta  = await fetch(pathControlador, { method: 'POST', body });
        const dato = await respuesta.json();
        if (dato.ok) {
            mostrarAlerta('success', dato.mensaje);
            evento.target.reset();
        } else {
            mostrarAlerta('danger', dato.error);
        }
    } catch {
        mostrarAlerta('danger', 'Error de conexión');
    } finally {
        spinner.classList.add('d-none');
        btn.disabled = false;
    }
});

// Datos laborales (solo empleados)
if (window.ID_ROL === 2) {
    fetch('/cwu/Controladores/EmpleadoControlador.php?action=obtenerPerfilEmpleado')
        .then(respuesta => respuesta.json())
        .then(datos => {
            if (!datos.ok) return;
            document.getElementById('especialidad').value    = datos.data.especialidad    ?? '';
            document.getElementById('precio_por_hora').value = datos.data.precio_por_hora ?? '';
        })

    document.getElementById('form-empleado').addEventListener('submit', async evento => {
        evento.preventDefault();
        const spinner = document.getElementById('spinner-empleado');
        const btn     = document.getElementById('btn-guardar-empleado');
        spinner.classList.remove('d-none');
        btn.disabled = true;

        try {
            const body = new FormData(evento.target);
            body.append('action', 'guardarPerfilEmpleado');

            const respuesta  = await fetch('/cwu/Controladores/EmpleadoControlador.php', { method: 'POST', body });
            const dato = await respuesta.json();

            if (dato.ok) { 
                mostrarAlerta('success', dato.mensaje);
            } else {
                mostrarAlerta('danger',  dato.error);
            }

        } catch {
            mostrarAlerta('danger', 'Error de conexión');
        } finally {
            spinner.classList.add('d-none');
            btn.disabled = false;
        }
    });
}

function mostrarAlerta(tipo, mensaje) {
    const alert = document.getElementById('perfil-alert');
    alert.innerHTML = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
