var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_marcas();
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#marca").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

function listar_marcas() {
    let isError = false;
    $.ajax({
        url: "sellin_controlador.php?op=listar_marcas",
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
            if(!jQuery.isEmptyObject(data.lista_marcas)){
                //lista de seleccion de vendedores
                $('#marca')
                    .append('<option name="" value="">Seleccione una opción</option>')
                    .append('<option name="" value="-">TODAS</option>');
                $.each(data.lista_marcas, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#marca').append('<option name="" value="' + opt.marca +'">' + opt.marca + '</option>');
                });
            }
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

var no_puede_estar_vacio = function()
{
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" && $("#marca").val() !== "")
        ? estado_minimizado = true : estado_minimizado = false ;
};

$(document).ready(function(){
    $("#fechai").change( () => no_puede_estar_vacio() );
    $("#fechaf").change( () => no_puede_estar_vacio() );
    $("#marca").change( () => no_puede_estar_vacio() );
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_sellin", function () {
    $("#sellin_data").dataTable().fnDestroy();
    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var marca = $("#marca").val();
    var tipo = $("#tipo").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "" && marca !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("marca", marca);
            sessionStorage.setItem("tipo", tipo);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#sellin_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "sellin_controlador.php?op=buscar_sellin",
                    type: "post",
                    dataType: "json",
                    data: { fechai:fechai, fechaf:fechaf, marca:marca, tipo:tipo },
                    beforeSend: function () {
                        SweetAlertLoadingShow();
                    },
                    error: function (e) {
                        isError = SweetAlertError(e.responseText, "Error!")
                        send_notification_error(e.responseText);
                        console.log(e.responseText);
                    },success: function (data) {

                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
    
                        //creamos una matriz JSON para aoColumnDefs
                        var aryJSONColTable = [];
                        /*var aryColTableChecked = data.columns;
                        for (var i = 0; i <aryColTableChecked.length; i ++) {
                            aryJSONColTable.push ({
                                "sTitle": aryColTableChecked[i],
                                "aTargets": [i]
                            });
                        }*/
                        $.each(data.columns, function(i, opt) {
                            aryJSONColTable.push({"sTitle": opt, "aTargets": [i]});
                        });
    
                        tabla = $('#sellin_data').DataTable({
                            "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                            "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                            "sEcho": data.sEcho, //INFORMACION PARA EL DATATABLE
                            "iTotalRecords": data.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
                            "iTotalDisplayRecords": data.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
                            "data": data.aaData, // informacion por registro
                            //CodigoDinamico
                            "aoColumnDefs": aryJSONColTable,
                            "bProcessing": true,
                            "bLengthChange": true,
                            "bFilter": true,
                            "bScrollCollapse": true,
                            "bJQueryUI": true,
                            //finCodigoDinamico
                            "bDestroy": true,
                            "bInfo": true,
                            "iDisplayLength": 10,
                            "language": texto_español_datatables
                        });
    
                    if($("#tipo").val()=='f') {

                        $('#total_registros').html(

                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Monto Total:   <code>' + data.Mtototal + '  Bs</code>   '
                        )

                    }else{
                        if ($("#tipo").val() == 'n') {

                            $('#total_registros').html(

                                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Monto Total:   <code>' + data.Mtototald + '  $</code>   '
                            )

                        }
                        $('#total_registros').html(

                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Monto Total:   <code>' + data.Mtototal + '  Bs</code>   '+
                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Monto Total:   <code>' + data.Mtototald + '  $</code>   '
                        )

                    }
                       

                        validarCantidadRegistrosTabla();
                        limpiar();
                    },
                    complete: function () {
                        if(!isError) SweetAlertLoadingClose();
                    }
                },
            });
            estado_minimizado = true;
        }
    } else {
        SweetAlertError('Debe seleccionar un rango de fecha y un marca.');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
   var fechai = sessionStorage.getItem("fechai", fechai);
   var fechaf = sessionStorage.getItem("fechaf", fechaf);
   var marca = sessionStorage.getItem("marca", marca);
    var tipo = sessionStorage.getItem("tipo", tipo);

    if (fechai !== "" && fechaf !== "" && marca !== "" && tipo !== "") {
       window.location = "sellin_excel.php?&fechai=" + fechai + "&fechaf=" + fechaf + "&marca=" + marca + "&tipo=" + tipo;
}
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var marca = sessionStorage.getItem("marca", marca);
    var tipo = sessionStorage.getItem("tipo", tipo);
    if (fechai !== "" && fechaf !== "" && marca !== "" && tipo !== "") {
        window.open('sellin_pdf.php?&fechai=' + fechai + '&fechaf=' + fechaf + '&marca=' + marca + '&tipo=' + tipo, '_blank');
    }
});

function mostrar() {

    var texto= 'Clientes Sin Transacción:  ';
    var cuenta =(tabla.rows().count());
    $("#cuenta").html(texto + cuenta);
}

init();
