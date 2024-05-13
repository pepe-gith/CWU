<?php
$conexion=mysqli_connect('localhost','root','','cwubd');
    if($conexion->connect_error):
        echo"Error en conexión a Base de Datos ".$conexion->connect_error;
    endif;
?>