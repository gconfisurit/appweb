var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function()
{
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "") ? estado_minimizado = true : estado_minimizado = false ;
};

$(document).ready(function(){
    $("#fechai").change( () => no_puede_estar_vacio() );
    $("#fechaf").change( () => no_puede_estar_vacio() );
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_clientesnuevos", function () {

    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#clientesnuevos_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "clientesnuevos_controlador.php?op=buscar_clientesnuevos",
                    type: "post",
                    dataType: "json",
                    data: {fechai: fechai, fechaf: fechaf},
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
                        validarCantidadRegistrosTabla();
                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        mostrar();
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
        SweetAlertError('Debe seleccionar un rango de Fecha!');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
   var fechai = sessionStorage.getItem("fechai", fechai);
   var fechaf = sessionStorage.getItem("fechaf", fechaf);
   if (fechai !== "" && fechaf !== "") {
       window.location = "clientesnuevos_excel.php?&fechai="+fechai+"&fechaf="+fechaf;
   }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    if (fechai !== "" && fechaf !== "") {
        window.open('clientesnuevos_pdf.php?&fechai='+fechai+'&fechaf='+fechaf, '_blank');
    }
});

function mostrar() {
    var texto= 'Clientes Nuevos: ';
    var cuenta =(tabla.rows().count());
    $("#cuenta").html(texto + cuenta);
}

init();
