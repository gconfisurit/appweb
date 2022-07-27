var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $("#loader").hide();
    estado_minimizado = false;
}

function limpiar() {
    $("#fechaf").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

$(document).ready(function(){
    $("#fechaf").change( () => estado_minimizado = true )
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_activacionclientes", function () {

    var fecha_final = $("#fechaf").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle(); //MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if(fecha_final !== "") {
            sessionStorage.setItem("fechaf", fecha_final);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            let isError = false;
            tabla = $('#activacionclientes_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "activacionclientes_controlador.php?op=buscar_activacionclientes",
                    type: "post",
                    dataType: "json",
                    data: {fecha_final: fecha_final},
                    beforeSend: function () {
                        SweetAlertLoadingShow();
                    },
                    error: function (e) {
                        isError = SweetAlertError(e.responseText, "Error!")
                        send_notification_error(e.responseText);
                        console.log(e.responseText);
                    },
                    complete: function () {
                        if(!isError) SweetAlertLoadingClose();
                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        validarCantidadRegistrosTabla();
                        limpiar();//LIMPIAMOS EL SELECTOR.
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
        SweetAlertError('Debe Ingresar una Fecha Tope!');
        return (false);
    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var fecha_final = sessionStorage.getItem("fechaf");
    if(fecha_final !== ""){
        window.location = "activacionclientes_excel.php?fecha_final="+fecha_final;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fecha_final = sessionStorage.getItem("fechaf");
    if(fecha_final !== ""){
        window.open('activacionclientes_pdf.php?&fecha_final='+fecha_final, '_blank');
    }
});

init();
