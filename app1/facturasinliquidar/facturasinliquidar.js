var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_chofer();
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#chofer").val("Todos");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function()
{
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" )
        ? estado_minimizado = true : estado_minimizado = false ;
};

function listar_chofer() {
    let isError = false;
    $.ajax({
        url: "facturasinliquidar_controlador.php?op=listar_chofer",
        method: "POST",
        dataType: "json",
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data.lista_chofer)){
                //lista de seleccion de proveedor
                $('#chofer').append('<option name="" value="Todos">Todos</option>');
                $.each(data.lista_chofer, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#chofer').append('<option name="" value="' + opt.cedula +'">'+opt.cedula+' - '+ opt.descripcion+ '</option>');
                });
            }
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}


$(document).ready(function(){
    $("#fechai").change( () => no_puede_estar_vacio() );
    $("#fechaf").change( () => no_puede_estar_vacio() );
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {
   // alert("si entra");
    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var chofer = $("#chofer").val();
    var tipo = $("#tipo").val();
    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "" && tipo !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("chofer", chofer);
            sessionStorage.setItem("tipo", tipo);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#facturasinliquidar_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "facturasinliquidar_controlador.php?op=buscar_facturasinliquidar",
                    type: "post",
                    dataType: "json",
                    data: {fechai: fechai, fechaf: fechaf, chofer:chofer, tipo:tipo},
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
                        //mostrar()
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
        SweetAlertError('Debe seleccionar un rango de fecha y el Tipo de transacción.');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
   var fechai = sessionStorage.getItem("fechai", fechai);
   var fechaf = sessionStorage.getItem("fechaf", fechaf);
   var chofer = sessionStorage.getItem("chofer", chofer);
   var tipo = sessionStorage.getItem("tipo", tipo);
   if (fechai !== "" && fechaf !== "") {
    window.location = "facturasinliquidar_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&chofer="+chofer+"&tipo="+tipo;
}
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    if (fechai !== "" && fechaf !== "") {
        window.open('relacionNE_pdf.php?&fechai='+fechai+'&fechaf='+fechaf, '_blank');
    }
});



init();
