var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_vendedores();
    listar_canales();
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#tipo").val("");
    $("#vendedores").val("");
    $("#checkbox").prop("checked", false);
}

function limpiar_modal_detalle_factura() {
    $("#numero_factura").text("");
    $("#descrip_detfactura").text("");
    $("#codusua_detfactura").text("");
    $("#fechae_detfactura").text("");
    $("#codvend_detfactura").text("");
    $('#tabla_detalle_factura tbody').empty();
    $("#factura_despachada").html("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" && $("#tipo").val() !== "" && $("#vendedores").val() !== "")
        ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function () {
    $("#fechai").change(() => no_puede_estar_vacio());
    $("#fechaf").change(() => no_puede_estar_vacio());
    $("#tipo").change(() => no_puede_estar_vacio());
    $("#vendedores").change(() => no_puede_estar_vacio());
});


//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_factsindes", function () {

    var fechai = $('#fechai').val();
    var fechaf = $('#fechaf').val();
    var tipo = $('#tipo').val();
    var vendedores = $('#vendedores').val();
    var check = ($('#checkbox:checked').val()!=null);


    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "" && tipo !== "" && vendedores !== "") {
            sesionStorageItems(fechai, fechaf, tipo, vendedores, check);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            $.ajax({
                async: true,
                url: "facturassindespachar_controlador.php?op=listar",
                method: "POST",
                dataType: "json",
                data: {'fechai': fechai,'fechaf': fechaf,'tipo': tipo, 'vendedores': vendedores, 'check': check,},
                beforeSend: function () {
                    SweetAlertLoadingShow();
                    if(tabla instanceof $.fn.dataTable.Api){
                        $('#tablafactsindes').DataTable().clear().destroy();
                    }
                    $('#tablafactsindes thead').empty();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {

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

                    tabla = $('#tablafactsindes').DataTable({
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
                        "responsive": (check),
                        "bInfo": true,
                        "iDisplayLength": 10,
                        "language": texto_español_datatables
                    });

                    var oportunidad = (data.oportunidad !== '') ? '&nbsp;&nbsp;&nbsp;&nbsp; % Oportunidad Total: <code>' + data.oportunidad + '</code>' : '';
                    $('#total_registros').html(
                        'Total de Documentos:     <code>' +data.totalDoc + '</code>' +
                        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Monto Total:   <code>' + data.Mtototal + 'BsS</code>   '
                        + oportunidad
                    )

                    validarCantidadRegistrosTabla();
                    limpiar();
                },
                complete: function () {
                    if(!isError) SweetAlertLoadingClose();
                }
            });
            estado_minimizado = true;
        }
    } else {

        if (fechai === ""){
            SweetAlertError('Debe Colocar la fecha inicial!');
            return false;
        }
        if (fechaf === "" ){
            SweetAlertError('Debe Colocar la fecha final!');
            return false;
        }
        if (vendedores === ""){
            SweetAlertError('Debe Seleccionar una Ruta!');
            return false;
        }
        if (tipo === ""){
            SweetAlertError('Debe Seleccionar un Tipo!');
            return false;
        }
    }
});

function sesionStorageItems(fechai, fechaf, tipo, vendedores, check){
    sessionStorage.setItem("fechai", fechai);
    sessionStorage.setItem("fechaf", fechaf);
    sessionStorage.setItem("tipo", tipo);
    sessionStorage.setItem("vendedores", vendedores);
    sessionStorage.setItem("check", check);
}

function listar_vendedores() {
    let isError = false;
    $.ajax({
        url: "facturassindespachar_controlador.php?op=listar_vendedores",
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
                $('#vendedores').append('<option name="" value="">Seleccione</option>');
                $('#vendedores').append('<option name="-" value="-">Todos</option>');
                $.each(data.lista_vendedores, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#vendedores').append('<option name="" value="' + opt.CodVend +'">' + opt.CodVend + ': ' + opt.Descrip.substr(0, 35) + '</option>');
                });
            }
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function listar_canales(){
    $.ajax({
        url: "facturassindespachar_controlador.php?op=listar_canales",
        method: "POST",
        dataType: "json",
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data.lista_canales)){
                //lista de seleccion de vendedores
                $('#tipo')
                    .append('<option name="" value="">Seleccione Tipo</option>')
                    .append('<option name="" value="-">Todos</option>');
                $.each(data.lista_canales, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#tipo').append('<option name="" value="' + opt.Clase +'">' + opt.Clase + '</option>');
                });
            }
        }
    });
}
function mostrarModalDetalleFactura(numerod, tipofac) {
    let isError = false;
    limpiar_modal_detalle_factura();
    $('#detallefactura').modal('show');

    $.ajax({
        url: "facturassindespachar_controlador.php?op=detalle_de_factura",
        method: "POST",
        dataType: "json",
        data: {numerod: numerod, tipofac: tipofac},
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            let { factura, factura_despachada} = data;

            if (factura.info.existeData === true)
            {
                let {cabecera, detalle, totales, info} = factura;

                //cabecera de la factura
                $("#numero_factura").text(numerod);
                $("#descrip_detfactura").text(cabecera.descrip);
                $("#codusua_detfactura").text(cabecera.codusua);
                $("#fechae_detfactura").text(cabecera.fechae);
                $("#codvend_detfactura").text(cabecera.codvend);

                //detalle de la factura
                $.each(detalle, function (idx, opt) {
                    //como puede hacer varios registros de productos en una factura se itera con each
                    $('#tabla_detalle_factura')
                        .append(
                            '<tr>' +
                            '<td align="center" class="small align-middle">' + opt.coditem + '</td>' +
                            '<td align="center" class="small align-middle">' + opt.descrip + '</td>' +
                            '<td align="center" class="small align-middle">' + opt.cantidad + '</td>' +
                            '<td align="center" class="small align-middle">' + opt.tipounid + '</td>' +
                            '<td align="center" class="small align-middle">' + opt.precio + '</td>' +
                            '<td align="center" class="small align-middle">' + opt.descuento + '</td>' +
                            '<td align="center" class="small align-middle">' + opt.totalitem + '</td>' +
                            '</tr>'
                        );
                });

                $('#tabla_detalle_factura')
                    .append( //separador
                        '<tr>' +
                        '<td colspan="7">===================================================================</td>' +
                        '</tr>'
                    )
                    .append( //totales de la factura
                        '<tr>' +
                        '<td align="center" class="small align-middle"> Total de Bultos ' + info.bultos + '</td>' +
                        '<td colspan="4"></td>' +
                        '<td align="center" class="small align-middle"><div align="right">Sub Total</div></td>' +
                        '<td align="center" class="small align-middle"><div align="center">' + totales.subtotal + '</div></td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td align="center" class="small align-middle"> Total de Paquetes ' + info.paquetes + '</td>' +
                        '<td colspan="4"></td>' +
                        '<td align="center" class="small align-middle"><div align="right">Descuento</div></td>' +
                        '<td align="center" class="small align-middle"><div align="center">' + totales.descuento + '</div></td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td colspan="5"></td>' +
                        '<td align="center" class="small align-middle"><div align="right">Excento</div></td>' +
                        '<td align="center" class="small align-middle"><div align="center">' + totales.exento + '</div></td>' +
                        '</tr>' +

                        '<tr>' +
                        '<td colspan="5"></td>' +
                        '<td align="center" class="small align-middle"><div align="right">Base Imponible</div></td>' +
                        '<td align="center" class="small align-middle"><div align="center">' + totales.base + '</div></td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td colspan="5"></td>' +
                        '<td align="center" class="small align-middle"><div align="right">Impuestos ' + data.iva + ' %</div></td>' +
                        '<td align="center" class="small align-middle"><div align="center">' + totales.impuesto + '</div></td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td colspan="5"></td>' +
                        '<td align="center" class="small align-middle"><div align="right">Monto Total</div></td>' +
                        '<td align="center" class="small align-middle"><div align="center">' + totales.total + '</div></td>' +
                        '</tr>'
                    );
            }
            $("#factura_despachada").html(factura_despachada);
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var fechai = sessionStorage.getItem("fechai");
    var fechaf = sessionStorage.getItem("fechaf");
    var tipo = sessionStorage.getItem("tipo");
    var vendedores = sessionStorage.getItem("vendedores");
    var check = sessionStorage.getItem("check");
    window.location = "facturassindespachar_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&tipo="+tipo+"&vendedores="+vendedores+"&check="+check;
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai");
    var fechaf = sessionStorage.getItem("fechaf");
    var tipo = sessionStorage.getItem("tipo");
    var vendedores = sessionStorage.getItem("vendedores");
    var check = sessionStorage.getItem("check");
    window.open("facturassindespachar_pdf.php?&fechai="+fechai+"&fechaf="+fechaf+"&tipo="+tipo+"&vendedores="+vendedores+"&check="+check, '_blank');
});

init();
