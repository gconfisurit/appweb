var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_proveedores();
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#proveedor").val("");
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

function listar_proveedores() {
    let isError = false;
    $.ajax({
        url: "analisisdevencimiento_controlador.php?op=listar_proveedores",
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
            if(!jQuery.isEmptyObject(data.lista_proveedores)){
                //lista de seleccion de proveedor
                $('#proveedor').append('<option name="" value="Todos">Todos</option>');
                $.each(data.lista_proveedores, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#proveedor').append('<option name="" value="' + opt.CodProv +'">'+ opt.Descrip+ '</option>');
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
    var proveedor = $("#proveedor").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("proveedor", proveedor);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#vencimiento_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "analisisdevencimiento_controlador.php?op=buscar_analisis",
                    type: "post",
                    dataType: "json",
                    data: {fechai: fechai, fechaf: fechaf, proveedor: proveedor},
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
        SweetAlertError('Debe seleccionar un rango de fecha.');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
   var fechai = sessionStorage.getItem("fechai", fechai);
   var fechaf = sessionStorage.getItem("fechaf", fechaf);
   var proveedor = sessionStorage.getItem("proveedor", proveedor);
   if (fechai !== "" && fechaf !== "") {
    window.location = "analisisdevencimiento_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&proveedor="+proveedor;
}
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var proveedor = sessionStorage.getItem("proveedor", proveedor);
    if (fechai !== "" && fechaf !== "") {
        window.open('analisisdevencimiento_pdf.php?&fechai='+fechai+'&fechaf='+fechaf+'&proveedor='+proveedor, '_blank');
    }
});

function mostrar() {

    var texto= 'Clientes No Activados: ';
    var cuenta =(tabla.rows().count());
    $("#cuenta").html(texto + cuenta);
}

init();
