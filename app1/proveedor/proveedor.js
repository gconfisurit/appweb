var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
}

function limpiar() {
    $("#orden").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    ( $("#orden").val() !== "") ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function () {
    $("#orden").change(() => no_puede_estar_vacio());
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_listadeproveedores", function () {
    var orden = $('#orden').val();
    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (orden !== "") {
            sessionStorage.setItem('orden',String);
           // sesionStorageItems(orden);
            let isError = false;

            tabla = $('#tablaproveedores').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "proveedor_controlador.php?op=listar",
                    type: "post",
                    dataType: "json",
                    data: {'orden': orden},
                    beforeSend: function () {
                        SweetAlertLoadingShow();
                    },
                    error: function (e) {
                        isError = SweetAlertError(e.responseText, "Error!")
                        send_notification_error(e.responseText);
                        console.log("Error!");
                    },
                    complete: function () {
                        if(!isError) SweetAlertLoadingClose();
                        validarCantidadRegistrosTabla();
                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        //mostrar();
                        //limpiar();//LIMPIAMOS EL SELECTOR.
                    }
                },//TRADUCCION DEL DATATABLE.
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 10,
                "order": [[0, "desc"]],
                "language": texto_español_datatables
            });

            estado_minimizado = true;
        }
    } else {

        if (orden === "") {
            SweetAlertError('Seleccione como desea realizar la consulta!');
            return (false);
        }
    }



 });


//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var orden = $('#orden').val();
    if (orden !== "") {
        sessionStorage.getItem("orden", orden);
        window.open ('proveedor_excel.php?&orden='+orden);
    }
 });
 
 //ACCION AL PRECIONAR EL BOTON PDF.
 $(document).on("click","#btn_pdf", function(){
    var orden = $('#orden').val();
        sessionStorage.getItem("orden", orden);
     if (orden !== "") {
        sessionStorage.getItem("orden", orden);
         window.open('proveedor_pdf.php?&orden='+orden, '_blank');
     }
 });


init();