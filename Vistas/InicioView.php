<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link  rel="stylesheet" href="/cwu/CSS/index.css" >
    <title>Document</title>
</head>
<body>
    
    <div class="cajon">
    <nav>
        <a href="InicioView.php">Inicio</a> |
        <a href="RegistroView.php">Registro</a> |
        <a href="AccesoView.php">Acceso</a> 
    </nav>

      <div class="contenedor">
            <div class="caja">  
                <router-link :to="{name: 'escape'}">
                    <img class="opc" src="../CSS/Img/escape.png" height="200" >
                </router-link>   
                    
            </div>
            
            <div class="caja">   
                <router-link :to="{name: 'feliz'}">
                    <img class="opc" src="../CSS/Img/feliz.png" height="200" >
                </router-link>   
                
            </div>
                
            <div class="caja">   
                <router-link :to="{name: 'fiesta'}">
                    <img class="opc" src="../CSS/Img/fiesta.png" height="200" >
                </router-link>   
        </div>
    </div>    
  
</body>
</html>

<!--    
    <script lang="ts" setup>
    </script>
    
    <style scoped>
    nav {
      margin-bottom: 30px;
      padding: 30px;
      text-align: right;
    
      a {
        font-weight: bold;
        color: #2c3e50;
    
        &.router-link-exact-active {
          color: #42b983;
        }
      }
    }
    
    .cajon {
        height: 800px;
        background-size: cover;
    }
    
    .lin1 {
        width:  100%;
    }
    .contenedor {
        
        display: flex;
        flex-wrap:  wrap;
        justify-content: space-evenly;
        width: 95%;
    }    
    
    .opc {
        margin-right: 50px;
        margin-bottom: 100px;
    }
    
    
    .caja:hover{
        transform: scale(1.4);
    
    }
    </style>
-->