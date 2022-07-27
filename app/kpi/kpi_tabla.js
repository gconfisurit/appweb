
let colspanRutas;
let colspanActivacion;
let colspanEfectividad;
let colspanVentas;
let colspanTotal;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    colspanRutas       = $('#cabecera_rutas').attr('colSpan');
    colspanActivacion  = $('#cabecera_activacion').attr('colSpan');
    colspanEfectividad = $('#cabecera_efectividad').attr('colSpan');
    colspanVentas      = $('#cabecera_ventas').attr('colSpan');

    colspanTotal = colspanRutas + colspanActivacion + colspanEfectividad + colspanVentas;

    listar_marcas();
    listar_kpi();
}

$(document).ready(function(){

});

function listar_kpi(){

    let fechai    = $('#fechai').val();
    let fechaf    = $('#fechaf').val();
    let d_habiles = $('#d_habiles').val();
    let d_trans   = $('#d_trans').val();

    $.ajax({
        url: "kpi_controlador.php?op=listar_kpi",
        method: "POST",
        data: {fechai: fechai, fechaf: fechaf, d_habiles: d_habiles, d_trans:d_trans},
        dataType: "json", // Formato de datos que se espera en la respuesta
        beforeSend: function () {
            SweetAlertLoadingShow("Procesando información, espere...");
        },
        error: function (e) {
            SweetAlertError(e.responseText.substring(0, 400) + "...", "Error!");
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (datos) {
            if(!jQuery.isEmptyObject(datos.tabla)){
                $.each(datos.tabla, function(idx, opt) {
                    let { coordinador, data, subtotal } = opt

                    $('#tabla').append('<tr><td class="text-left" colspan="'+colspanTotal+'">Coordinador:   <strong>' + coordinador.toUpperCase() + '</strong></td></tr>');

                    $.each(data, function(idx, opt) {
                        $('#tabla').append(obtenerInfoTabla(opt));
                    });

                    $('#tabla').append(obtenerInfoTabla(subtotal, true, true));
                });
            }
            $('#tabla').append('<tr><td colspan="'+colspanTotal+'">'+ "" +'</td></tr>');
            $('#tabla').append(obtenerInfoTabla(datos.total_general, true, true));
        },
        complete: function () {
            SweetAlertSuccessLoading()

            $('#tabla').columntoggle({
                //Class of column toggle contains toggle link
                toggleContainerClass:'columntoggle-container',
                //Text in column toggle box
                toggleLabel:'MOSTRAR/OCULTAR CELDAS: ',
                //the prefix of key in localstorage
                keyPrefix:'columntoggle-',
                //keyname in localstorage, if empty, it will get from URL
                key:''

            });
        }
    });
}

function listar_marcas() {
    let isError = false;
    $.ajax({
        async: false,
        url: "kpi_controlador.php?op=listar_marcaskpi",
        type: "GET",
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            data = JSON.parse(data);

            if(!jQuery.isEmptyObject(data.lista_marcaskpi)){
                $('#cabecera_activacion').attr('colSpan', (parseInt(colspanActivacion) + parseInt(data.lista_marcaskpi.length)) );

                $.each(data.lista_marcaskpi, function(idx, opt) {
                    $('table thead #cells').find('th:nth-child('+3+')').after(
                        '<th style="width: 20px;"><div class="small align-middle" style="width: 10px; word-wrap: break-word; text-align: center">' + opt + '</div></th>'
                    );
                });
            }
        }
    });
}

function mostrarDetalleEdv(edv) {
    let isError = false;
    $('#detalleEdvModal').modal('show');
    $('#descrip_edv_title').html(edv);

    $.ajax({
        url: "kpi_controlador.php?op=mostrar_detalle_edv",
        type: "POST",
        dataType: "json",
        data: {edv: edv},
        beforeSend: function () {
            SweetAlertLoadingShow();
            $('#tabla_detalle_edv tbody').empty();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            $.each(data.detalle_edv, function(idx, opt) {
                $('#tabla_detalle_edv').append(
                    '<tr>' +
                    '<td class="text-right" style="font-weight: bold; width: 30%">' + opt[0] + '</td>' +
                    '<td class="text-left" style="font-weight: normal; width: 70%">' + opt[1] + '</td>' +
                    '</tr>');
            });
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function mostrarListaClientes(edv, flag) {
    let isError = false;
    let typeModal;
    let title;
    $('#listaClientesModal').modal('show');
    let fechai = $('#fechai').val();
    let fechaf = $('#fechaf').val();

    switch (flag) {
        case 1:
            typeModal = 'listar_maestro_clientes';
            title = 'Maestro de Clientes';
            break;
        case 2:
            typeModal = 'listar_clientes_activados';
            title = 'Clientes Activados';
            break;
        case 3:
            typeModal = 'listar_clientes_pendientes';
            title = 'Clientes No Activados';
            break;
    }

    $('#tipo_lista_clientes_title').html(title);
    $('#descrip_lista_clientes_edv').html(edv);

    $('#tabla_lista_clientes').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'kpi_controlador.php?op='+typeModal,
                type: "post",
                dataType: "json",
                data: {edv: edv, fechai: fechai, fechaf:fechaf},
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
                }
            },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,//Por cada 10 registros hace una paginación
        "order": [[0, "asc"]],//Ordenar (columna,orden)
        "language": texto_español_datatables
    }).DataTable();
}

function mostrarActivacionPorMarca(edv, marca) {
    let isError = false;
    $('#activacionPorMarcaModal').modal('show');
    let fechai = $('#fechai').val();
    let fechaf = $('#fechaf').val();

    $('#descrip_activacion_marca').html(edv);
    $('#marca_descrip').html(marca);

    $('#tabla_activacion_marca').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'kpi_controlador.php?op=listar_activacion_marcas',
                type: "post",
                dataType: "json",
                data: {edv: edv, marca:marca, fechai: fechai, fechaf:fechaf},
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
                }
            },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,//Por cada 10 registros hace una paginación
        "order": [[1, "asc"]],//Ordenar (columna,orden)
        "language": texto_español_datatables
    }).DataTable();
}

function mostrarListaDocumentos(edv, flag) {
    let isError = false;
    let typeModal;
    let title;
    $('#listaDocumentosModal').modal('show');
    let fechai = $('#fechai').val();
    let fechaf = $('#fechaf').val();

    switch (flag) {
        case 1:
            typeModal = 'listar_facturas_realizadas';
            title = 'Facturas Realizadas';
            break;
        case 2:
            typeModal = 'listar_notas_realizadas';
            title = 'Notas Realizadas';
            break;
        case 3:
            typeModal = 'listar_devoluciones_realizadas';
            title = 'Devoluciones Realizadas';
            break;
        case 4:
            typeModal = 'listar_cobranzas_rebajadas';
            title = 'Cobranzas Rebajadas';
            break;
    }

    $('#tipo_lista_documentos_title').html(title);
    $('#descrip_edv_documentos').html(edv);

    $('#thead_monto').html( (flag===4) ? 'Monto Rebajado' : 'Monto');
    $('#tfoot_monto').html( (flag===4) ? 'Monto Rebajado' : 'Monto');

    $('#tabla_documentos').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'kpi_controlador.php?op='+typeModal,
                type: "post",
                dataType: "json",
                data: {edv: edv, fechai: fechai, fechaf:fechaf},
                beforeSend: function () {
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText, "Error!")
                    console.log(e.responseText);
                },
                complete: function () {
                    if(!isError) SweetAlertLoadingClose();
                }
            },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,//Por cada 10 registros hace una paginación
        "order": [[0, "asc"]],//Ordenar (columna,orden)
        "language": texto_español_datatables
    }).DataTable();
}

function obtenerInfoTabla(opt, negrita=false, colorFull=false) {
    isBold  = negrita===true ? 'style="font-weight: bold"' : "";

    // se toma como referencia para la condicion si es negrita, debido a que si es TRUE es que es subtotal o totalgeneral. en caso contrario es algun edv
    ruta = negrita===true ? opt.ruta : '<a data-toggle="modal" onclick="mostrarDetalleEdv(\''+opt.ruta+'\')" data-target="#detalleEdvModal" href="#">' +opt.ruta+ '</a>' ;
    maestro = negrita===true ? opt.maestro : '<a data-toggle="modal" onclick="mostrarListaClientes(\''+opt.ruta+'\', 1)" data-target="#listaClientesModal" href="#">' +opt.maestro+ '</a>' ;
    clientes_activados = negrita===true ? opt.activos : '<a data-toggle="modal" onclick="mostrarListaClientes(\''+opt.ruta+'\', 2)" data-target="#listaClientesModal" href="#">' +opt.activos+ '</a>' ;
    clientes_pendientes = negrita===true ? opt.por_activar : '<a data-toggle="modal" onclick="mostrarListaClientes(\''+opt.ruta+'\', 3)" data-target="#listaClientesModal" href="#">' +opt.por_activar+ '</a>' ;
    facturas_realizadas = negrita===true ? opt.facturas_realizadas : '<a data-toggle="modal" onclick="mostrarListaDocumentos(\''+opt.ruta+'\', 1)" data-target="#listaDocumentosModal" href="#">' +opt.facturas_realizadas+ '</a>' ;
    notas_realizadas = negrita===true ? opt.notas_realizadas : '<a data-toggle="modal" onclick="mostrarListaDocumentos(\''+opt.ruta+'\', 2)" data-target="#listaDocumentosModal" href="#">' +opt.notas_realizadas+ '</a>' ;
    devoluciones_realizadas = negrita===true ? opt.devoluciones_realizadas : '<a data-toggle="modal" onclick="mostrarListaDocumentos(\''+opt.ruta+'\', 3)" data-target="#listaDocumentosModal" href="#">' +opt.devoluciones_realizadas+ '</a>' ;
    cobranzas_rebajadas = negrita===true ? opt.cobranzas_rebajadas : '<a data-toggle="modal" onclick="mostrarListaDocumentos(\''+opt.ruta+'\', 4)" data-target="#listaDocumentosModal" href="#">' +opt.cobranzas_rebajadas+ '</a>' ;

    let valuesMarcas = '';
    opt.marcas.forEach( val => { valuesMarcas += '<td align="center" class="small align-middle" '+isBold+'>' + (negrita===true ? val.valor : '<a data-toggle="modal" onclick="mostrarActivacionPorMarca(\''+opt.ruta+'\', \''+val.marca+'\')" data-target="#activacionPorMarcaModal" href="#">'+ val.valor +'</a>') + '</td>' });

    return  '<tr>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + ruta + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + maestro + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + clientes_activados + '</td>' +
            valuesMarcas +
            td_withprogress(opt.porc_activacion, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + clientes_pendientes + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.visita + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.obj_documentos_mensual + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + facturas_realizadas + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + notas_realizadas + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + devoluciones_realizadas + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.montoendivisa_devoluciones + '</td>' +
            td_withprogress(opt.efec_alcanzada_fecha, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.objetivo_bulto + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.logro_bulto + '</td>' +
            td_withprogress(opt.porc_alcanzado_bulto, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.objetivo_kg + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.logro_kg + '</td>' +
            td_withprogress(opt.porc_alcanzado_kg, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.drop_size_divisas + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.objetivo_ventas_divisas + '</td>' +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.logro_ventas_divisas + '</td>' +
            td_withprogress(opt.porc_alcanzado_ventas_divisas, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.logro_ventas_divisas_pepsico + '</td>' +
            td_withprogress(opt.porc_alcanzado_ventas_divisas_pepsico, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + opt.logro_ventas_divisas_complementaria + '</td>' +
            td_withprogress(opt.porc_alcanzado_ventas_divisas_complementaria, isBold, colorFull) +
            '<td align="center" class="small align-middle" '+isBold+'>' + cobranzas_rebajadas + '</td>' +
            '</tr>';
}

let td_withprogress = (valor, isBold, withCellColor) => {
    if(withCellColor)
        return '<td align="center" class="small align-middle '+semaforo(valor)+'" '+isBold+'>' + valor + ' %</td>'
    return '<td align="center" class="small align-middle" '+isBold+'>' + obtenerProgress(valor) + '</td>'
}

function obtenerProgress(valor) {
    valor = parseFloat(valor.replace('.','').replace(',','.'));
    return '<span>'+valor+'%</span>' +
        '<div class="progress progress-xs">' +
                '<div class="progress-bar '+semaforo(valor)+'" style="width: '+valor+'%"></div>' +
            '</div>'
}

function semaforo(valor) {
    let bg;
    if (!is_float(valor)) {
        valor = parseFloat(valor.toString().replace('.','').replace(',','.'));
    }
    if (valor > 80){
        bg = "bg-success";
    }else if (valor > 50 && valor <= 80){
        bg = "bg-warning";
    }else if (valor <= 50){
        bg = "bg-danger";
    }
    return bg;
}

init();