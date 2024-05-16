
function inserta(){
  
    var NIF=$("#NIF").val();       
    var nombrecli=$("#nombrecli").val();
    var apellidos=$("#apellidos").val();
    var movil1=$("#movil1").val();
    var movil2= $("#movil2").val();
    var corre1=$("#corre1").val();
    var corre2=$("#corre2").val();
    var contra=$("#contra").val();
    var direccion=$("#direccion").val();
    var como=$("#como").val();
    
    
$.ajax({
         type:"POST",
          url: ".././Controladores/insertarCliente.php",
         data: {
            NIF:NIF,
            nombrecli:nombrecli,
            apellidos:apellidos,
            movil1:movil1,
            movil2:movil2,
            corre1:corre1,
            corre2:corre2,
            contra:contra,
            direccion:direccion,
            como:como
            
         } ,
         success : function(response){
            
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