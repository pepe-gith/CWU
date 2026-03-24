fetch('../Controladores/mostrarUsuarios.php')
    .then(res => res.json())
    .then(data => {
        if (!data.ok) return;

        const tbody = document.querySelector('#cuerpoTablaUsuarios');

        data.datos.forEach(usuario => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${usuario.nif ?? ''}</td>
                <td>${usuario.nombre ?? ''}</td>
                <td>${usuario.apellidos ?? ''}</td>
                <td>${usuario.telefono ?? ''}</td>
                <td>${usuario.otro_telefono ?? ''}</td>
                <td>${usuario.email ?? ''}</td>
                <td>${usuario.direccion ?? ''}</td>
                <td>${usuario.como_conoce ?? ''}</td>
            `;
            tbody.appendChild(fila);
        });
    })
    .catch(error => {
        console.error('Error al cargar usuarios:', error.message);
    });
