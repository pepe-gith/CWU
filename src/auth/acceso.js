const form = document.querySelector('#formComprobarAcceso')
if (!form) throw new Error('AccesoView not found')

const errorMsg = document.getElementById('error-msg')

form.addEventListener('submit', function(event) {
    event.preventDefault()
    errorMsg.classList.add('d-none')

    const formData = new FormData(this)

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
