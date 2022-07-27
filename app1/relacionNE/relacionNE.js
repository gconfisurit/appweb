var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_rutas();
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#ruta").val("Todos");
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

function listar_rutas() {
    let isError = false;
    $.ajax({
        url: "relacionNE_controlador.php?op=listar_vendedores",
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
                //lista de seleccion de proveedor
                $('#ruta').append('<option name="" value="Todos">Todos</option>');
                $.each(data.lista_vendedores, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#ruta').append('<option name="" value="' + opt.CodVend +'">'+opt.CodVend+' - '+ opt.Descrip+ '</option>');
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
    $("#relacionNE_data").dataTable().fnDestroy();
    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var ruta = $("#ruta").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("ruta", ruta);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#relacionNE_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax":  {
                    url: "relacionNE_controlador.php?op=buscar_notaentrega",
                    type: "post",
                    dataType: "json",
                    data: {fechai: fechai, fechaf: fechaf, ruta:ruta},
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
    
                        tabla = $('#relacionNE_data').DataTable({
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
                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Monto Total:   <code>' + data.Mtototal + '  $</code>   '
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
        SweetAlertError('Debe seleccionar un rango de fecha.');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
   var fechai = sessionStorage.getItem("fechai", fechai);
   var fechaf = sessionStorage.getItem("fechaf", fechaf);
   var ruta = sessionStorage.getItem("ruta", ruta);
   if (fechai !== "" && fechaf !== "" && ruta !== "") {
    window.location = "relacionNE_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&ruta="+ruta;
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

function mostrar() {

    var texto= 'Clientes No Activados: ';
    var cuenta =(tabla.rows().count());
    $("#cuenta").html(texto + cuenta);
}

init();
