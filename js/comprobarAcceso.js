

const form = document.querySelector('#formComprobarAcceso');

form.addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch('../Controladores/comprobarAcceso.php', {
        method: "POST",
        body: formData
    })
    .then(async res => {
        const data = await res.json();

        if (!res.ok) {
            // error del servidor (400, 401, etc.)
            throw new Error(data.error);
        }

        return data;
    })
    .then(data => {
        console.log('OK:', data.mensaje);

        alert('Acceso correcto, redirigiendo a la página de inicio.');
        location.href = data.redirect;
    })
    .catch(error => {
        console.error('Error:', error.message);
    });
    
});
