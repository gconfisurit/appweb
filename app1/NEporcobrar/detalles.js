var tabla;

function init() {
    $("#tabla_detalle").hide();
    datadetalles();
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

//ACCION AL PRECIONAR EL BOTON.
function datadetalles() {

    var queryString = window.location.search;
    var urlParams = new URLSearchParams(queryString);
    var fechai = urlParams.get('fechai');
    var fechaf = urlParams.get('fechaf');
    var dataop = urlParams.get('data');

//alert( fechai +' '+ fechaf +' '+ dataop );

        $("#tabla_detalle").hide();///MINIMIZAMOS LA TARJETA.
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("dataop", dataop);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#cobrar_data_detalle').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "NEporcobrar_controlador.php?op=detalle_de_facturas",
                    type: "post",
                    dataType: "json",
                    data: {fechai: fechai, fechaf: fechaf , dataop:dataop},
                    beforeSend: function () {
                        SweetAlertLoadingShow();
                    },
                    error: function (e) {
                        isError = SweetAlertError(e.responseText, "Error!")
                        send_notification_error(e.responseText);
                        console.log(e.responseText);
                    },success: function (data) {

                        $("#tabla_detalle").show('');//MOSTRAMOS LA TABLA.
    
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
    
                        tabla = $('#cobrar_data_detalle').DataTable({
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
                        //limpiar();
                    },
                    complete: function () {
                        if(!isError) SweetAlertLoadingClose();
                    }
                }, 
            });
    
};

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel_detalle", function(){
    var queryString = window.location.search;
    var urlParams = new URLSearchParams(queryString);
    var fechai = urlParams.get('fechai');
    var fechaf = urlParams.get('fechaf');
    var dataop = urlParams.get('data');

    window.location = "NEporcobrardetalles_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&data="+dataop;

});

init();
