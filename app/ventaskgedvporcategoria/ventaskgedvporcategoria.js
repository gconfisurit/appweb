var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_vendedores();
    listar_instancias();
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#vendedor").val("");
    $("#instancia").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function()
{
    ($("#fechai").val() !== "" && $("#fechaf").val() !== ""
        && $("#vendedor").val() !== "" && $("#marca").val() !== "")
        ? estado_minimizado = true : estado_minimizado = false ;
};

function listar_vendedores() {
    $.ajax({
        url: "ventaskgedvporcategoria_controlador.php?op=listar_vendedores",
        method: "POST",
        dataType: "json",
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data.lista_vendedores)){
                //lista de seleccion
                $('#vendedor').append('<option name="" value="">--Seleccione vendedor--</option>');
                $('#vendedor').append('<option name="" value="-">TODOS</option>');
                $.each(data.lista_vendedores, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#vendedor').append('<option name="" value="' + opt.CodVend +'">' + opt.CodVend + ': ' + opt.Descrip.substr(0, 35) + '</option>');
                });
            }
        }
    });
}

function listar_instancias() {
    $.ajax({
        url: "ventaskgedvporcategoria_controlador.php?op=listar_instancias",
        method: "POST",
        dataType: "json",
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data.lista_instancias)){
                //lista de seleccion
                $('#instancia').append('<option name="" value="">--Seleccione instancia--</option>');
                $('#instancia').append('<option name="" value="-">TODAS</option>');
                $.each(data.lista_instancias, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#instancia').append('<option name="" value="' + opt.codinst +'">' + opt.descrip + '</option>');
                });
            }
        }
    });
}

$(document).ready(function(){
    $("#fechai").change( () => no_puede_estar_vacio() );
    $("#fechaf").change( () => no_puede_estar_vacio() );
    $("#vendedor").change( () => no_puede_estar_vacio() );
    $("#instancia").change( () => no_puede_estar_vacio() );
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {
    $("#ventaskgedvporcategoria_data").dataTable().fnDestroy();
    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var vendedor = $("#vendedor").val();
    var instancia = '';

    if ($("#instanciad").val()) {
        instancia = $("#instanciad").val();
    } else {
        instancia = $("#instancia").val();
    }

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "" && vendedor !== "" && instancia !== "") {
           
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("vendedor", vendedor);
            sessionStorage.setItem("instancia", instancia);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#ventaskgedvporcategoria_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax":{
                    url: "ventaskgedvporcategoria_controlador.php?op=listar",
                    type: "post",
                    dataType: "json",
                    data: {fechai: fechai, fechaf: fechaf, vendedor: vendedor, instancia: instancia},
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
    
                        tabla = $('#ventaskgedvporcategoria_data').DataTable({
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
                            'columnDefs':[
                                {
                                    'visible': true,
                                    'targets': [0]
                                }
                            ],
                            "bInfo": true,
                            "iDisplayLength": 10,
                            "language": texto_español_datatables
                        });
    
                        $('#total_registros').html(

                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Unidad Total:   <code>' + data.totalCant + '  Bultos</code>   '+
                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Kilogramo Total:   <code>' + data.totalPeso + '  Kg</code>   '+
                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Monto Total:   <code>' + data.totalMonto + '  Bs</code>   '
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
        SweetAlertError('Debe seleccionar un rango de fecha, instancia y un vendedor.');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var vendedor = sessionStorage.getItem("vendedor", vendedor);
    var instancia = sessionStorage.getItem("instancia", instancia);
    if (fechai !== "" && fechaf !== "" && vendedor !== "" && instancia !== "") {
        window.location = "ventaskgedvporcategoria_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&vendedor="+vendedor+"&instancia="+instancia;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var vendedor = sessionStorage.getItem("vendedor", vendedor);
    var instancia = sessionStorage.getItem("instancia", instancia);
    if (fechai !== "" && fechaf !== "" && vendedor !== "" && instancia !== "") {
        window.open('ventaskgedvporcategoria_pdf.php?&fechai='+fechai+'&fechaf='+fechaf+'&vendedor='+vendedor+"&instancia="+instancia, '_blank');
    }
});

init();
