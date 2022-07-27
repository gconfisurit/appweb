var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_vendedores();
}

function limpiar() {
    $("#vendedor").val("");
}

function listar_vendedores() {
    let isError = false;
    $.ajax({
        url: "NEcobros_controlador.php?op=listar_vendedores",
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
                $('#vendedor').append('<option name="" value="">Seleccione</option>');
                $('#vendedor').append('<option name="" value="Todos">Todos</option>');
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
function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}
var no_puede_estar_vacio = function()
{
    ( $("#vendedor").val() !== "")
        ? estado_minimizado = true : estado_minimizado = false ;
};


$(document).ready(function(){
    $("#vendedor").change( () => no_puede_estar_vacio() );
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {
    $("#cliente_data").dataTable().fnDestroy();
    var vendedor = $("#vendedor").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.  
        estado_minimizado = false;
        if ( vendedor != "") {
            sessionStorage.setItem("vendedor", vendedor);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#cliente_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax":{
                    url: "NEcobros_controlador.php?op=buscar_cliente",
                    type: "post",
                    dataType: "json",
                    data: {vendedor:vendedor},
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
    
                        tabla = $('#cliente_data').DataTable({
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
    
                        $('#total_registros').html(
                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Total Clientes:   <code>' + data.Mtototal + '</code>   '+
                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Clientes Activos:   <code>' + data.activos + '</code>   '+
                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Clientes Inactivos:   <code>' + data.inactivos + '</code>   '
                        )
    
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
        SweetAlertError('Debe seleccionar un vendedor.');
        return (false);

    }
});


//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
   var vendedor = sessionStorage.getItem("vendedor", vendedor);
   if (vendedor !== "") {
    window.location = "cliente_excel.php?&vendedor="+vendedor;
}
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var vendedor = sessionStorage.getItem("vendedor", vendedor);
    if (vendedor !== "") {
        window.open('cliente_pdf.php?&vendedor='+vendedor, '_blank');
    }
});



init();
