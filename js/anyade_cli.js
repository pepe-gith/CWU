const form = document.querySelector('#formRegistro');

form.addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch('../Controladores/crearUsuario.php', {
        method: "POST",
        body: formData
    }).then(async res => {
        const data = await res.json();
        if (data.ok) {
            alert('Cliente dado de alta, ya puede acceder con NIF y contraseña.');
            location.href = "../index.php";
            // location.href = "../RegistroView.php";
        } else {
            alert('Error al dar de alta Cliente: ' + data.error);
        }
    }).catch(error => {
        console.error('Error al dar de alta Cliente:', error.message);
        alert('Error al dar de alta Cliente: ' + error.message);
    });

});


    // fetch('../Controladores/crearCliente.php', {
    //     method: "POST",
    //     body: formData
    // }).catch(error => {
    //     console.log('esto ha dado error');
    // });
    