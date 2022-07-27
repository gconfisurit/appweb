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
    $("#tipo").val("Todos");
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
        url: "devoluciones_controlador.php?op=listar_vendedores",
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
    $("#devoluciones_data").dataTable().fnDestroy();
    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var ruta = $("#ruta").val();
    var tipo = $("#tipo").val();
    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("ruta", ruta);
            sessionStorage.setItem("tipo", tipo);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#devoluciones_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "devoluciones_controlador.php?op=buscar_devoluciones",
                    type: "post",
                    dataType: "json",
                    data: {fechai: fechai, fechaf: fechaf, ruta:ruta, tipo:tipo},
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
    
                        tabla = $('#devoluciones_data').DataTable({
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
    
                        if(tipo == 'B'){
                            var condicion = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Monto Total:   <code>' + data.Mtototal + '  Bs</code>   '
                        }else{

                            if(tipo == 'D'){
                                var condicion = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Monto Total:   <code>' + data.Mtototal + '  $</code>   '
                            }else{
                                var condicion = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Según el tipo de busqueda, los montos estan confinados en Bolivares y Dolares.   '
                            }

                            
                            
                        }
    
                        $('#total_registros').html(

                            condicion
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
   var tipo = sessionStorage.getItem("tipo", tipo);
   if (fechai !== "" && fechaf !== "") {
    window.location = "devoluciones_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&ruta="+ruta+"&tipo="+tipo;
}
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    if (fechai !== "" && fechaf !== "") {
        window.open('devolucionesE_pdf.php?&fechai='+fechai+'&fechaf='+fechaf, '_blank');
    }
});

function mostrar() {

    var texto= 'Clientes No Activados: ';
    var cuenta =(tabla.rows().count());
    $("#cuenta").html(texto + cuenta);
}

function obtener_datos(ndevolucion, tipo) {


$("#editarMotivo").submit(function (event) {
    $('#btnGuardarmotivo').attr("disabled", true);
    var queryString = window.location.search;
    var parametros = $(this).serialize();

    var motivo = $('#motivo').val();
    alert(tipo);

    $.ajax({
        type: "POST",
        url: "devoluciones_controlador.php?op=editarMotivo",
        data: { ndevolucion:ndevolucion , motivo:motivo , tipo:tipo},
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            
            console.log(e.responseText);
        },
        success: function (datos) {
           // $("#resultados_ajax_entregable_editar").html(datos);
            $('#btnGuardarmotivo').attr("disabled", false);
            let { icono, mensaje } = datos;
            ToastSweetMenssage(icono, mensaje);
            $('#editarMotivo').modal('hide');

                setTimeout(function () {
                    window.location.reload();
                }, 1000);
            

        }
    });
    event.preventDefault();
});

}

init();
