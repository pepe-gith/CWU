
function inserta(){
            alert("hola llego aqui");
    var Nif=$("#nif").val();       
    var NombreCli=$("#nombrecli").val();
    var Apellidos=$("#apellidos").val();
    var Movil1=$("#movil1").val();
    var Movil2= $("#movil2").val();
    var Corre1=$("#corre1").val();
    var Corre2=$("#corre2").val();
    var Contra=$("#contra").val();
    var Direccion=$("#direccion").val();
    var Como=$("#como").val();
    
    
$.ajax({
         type:"POST",
          url: "http://localhost/cwu/Controladores/insertarCliente.php",
         data: {
            Nif:Nif,
            Apellidos:Apellidos,
            Nombrecli:NombreCli,
            Movil1:Movil1,
            Movil2:Movil2,
            Corre1:Corre1,
            Corre2:Corre2,
            Contra:Contra,
            Direccion:Direccion,
            Como:Como
            
         } ,
         success : function(response){
            alert("ha enviado datos el AJAX")
             if(!response.error){
                 $("#users-form").trigger("reset");
                 alert("Cliente dado de alta, ya puede acceder con NIF y contraseña.");
                 location.href = "../index.php";
                }else {
                    alert("Error al dar de alta Cliente.");
             
             }  
            }
        });
        }