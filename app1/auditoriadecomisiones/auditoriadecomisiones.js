var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_vendedores();
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
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" )
        ? estado_minimizado = true : estado_minimizado = false ;
};

function listar_vendedores() {
    let isError = false;
    $.ajax({
        url: "auditoriadecomisiones_controlador.php?op=listar_vendedores",
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
            if(!jQuery.isEmptyObject(data.lista_vendedores)){
                //lista de seleccion de vendedores
                $('#vendedor').append('<option name="" value="">TODOS</option>');
                $.each(data.lista_vendedores, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#vendedor').append('<option name="" value="' + opt.CodVend +'">' + opt.CodVend + ': ' + opt.Descrip.substr(0, 35) + '</option>');
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

    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var vendedor = $("#vendedor").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("vendedor", vendedor);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#tabla_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "auditoriadecomisiones_controlador.php?op=buscar_auditoriadecomisiones",
                    type: "post",
                    dataType: "json",
                    data: {fechai: fechai, fechaf: fechaf, vendedor: vendedor},
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
        SweetAlertError('Debe seleccionar un rango de fecha y un vendedor.');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
   var fechai = sessionStorage.getItem("fechai", fechai);
   var fechaf = sessionStorage.getItem("fechaf", fechaf);
   var vendedor = sessionStorage.getItem("vendedor", vendedor);
   if (fechai !== "" && fechaf !== "") {
    window.location = "auditoriadecomisiones_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&vendedor="+vendedor;
}
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var vendedor = sessionStorage.getItem("vendedor", vendedor);
    if (fechai !== "" && fechaf !== "" && vendedor !== "") {
        window.open('auditoriadecomisiones_pdf.php?&fechai='+fechai+'&fechaf='+fechaf+'&vendedor='+vendedor, '_blank');
    }
});

init();
