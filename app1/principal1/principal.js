
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    fetch_pordespachar();
    fetch_porfacturar();
    fetch_cxc();
    fetch_cxp();
    cargar_grafica_ventasXmesdivisas();
    cargar_grafica_ventasXmesBULTOS();
    fetch_inventario_valorizado();
    fetch_clientes();
    fetch_total_ventas_mes_encurso();
    fetch_tasa_dolar();
    fetch_devoluciones_sin_motivo();
    fetch_top_marcas();
    fetch_top_clientes();
    fetch_top_productos();
}

function fetch_pordespachar() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_documentos_pordespachar",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_docPorDespachar').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { por_despachar } = data;
                $('#docPorDespachar').text(por_despachar);
            }
        },
        complete: function () {
            if(!isError) $('#loader_docPorDespachar').hide();
        }
    });
}

function fetch_porfacturar() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_pedidos_porfacturar",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_pedPorFacturar').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { por_facturar } = data;
                $('#pedsPorFacturar').text(por_facturar);
            }
        },
        complete: function () {
            if(!isError) $('#loader_pedPorFacturar').hide();
        }
    });
}

function fetch_cxc() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_cxc",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_cxc').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { cxc_bs, cxc_bs_dolar, cxc_$ } = data;
                $('#cxc_in_dolar').text(cxc_$);
                $('#cxc_in_bs').text(cxc_bs);
                $('#cxc_in_bs_dolar').text(cxc_bs_dolar);
            }
        },
        complete: function () {
            if(!isError) $('#loader_cxc').hide();
        }
    });
}

function fetch_cxp() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_cxp",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_cxp').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { cxp_bs, cxp_$ } = data;
                $('#cxp_in_dolar').text(cxp_$);
                $('#cxp_in_bs').text(cxp_bs);
            }
        },
        complete: function () {
            if(!isError) $('#loader_cxp').hide();
        }
    });
}

function cargar_grafica_ventasXmesdivisas() {
    let isError = false;
    $.ajax({
        cache: false,
        async: false,
        url: "principal/principal_controlador.php?op=buscar_ventasPormesdivisas",
        type: "post",
        dataType: "json",
        beforeSend: function () {
            $('#loader_ventas_por_mes').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if (!jQuery.isEmptyObject(data)) {
                let { anio, cantidad_meses_evaluar, datos, valor_mas_alto } = data;

                $('#title_ventas').text('del año ' + anio);

                let labels=[], values=[];
                if(!jQuery.isEmptyObject(datos)) {
                    //titulos de las barras
                    labels = datos[0].ventas_ano_actual.map( val => { return val.mes; });

                    //acumulado de ventas
                    $('#acum_ventas_anio_actual')
                        .html(`${sum(datos[0].ventas_ano_actual.map( val => {return parseFloat(val.valor);}))
                            .format_money(2, 3, '.', ',')}</span><sup style="font-size: 18px">$</sup>`
                    );

                    //simbolizacion ventas desde mes pasado
                    const porcentaje = incremento_porcentual_ventas(datos[0].ventas_ano_actual);
                    $('.incremento_ventas').removeClass('text-success').addClass((porcentaje>=0)?'text-success':'text-danger')
                    $('.incremento_ventas').html(`<i class="fas fa-arrow-${(porcentaje>=0)?'up':'dowm'}"></i> ${porcentaje.format_money(2, 3, '.', ',')} %`);

                    //valores de las barras
                    values[0] = get_values(datos[0].ventas_ano_actual, cantidad_meses_evaluar);
                    values[1] = get_values(datos[1].ventas_ano_anterior, cantidad_meses_evaluar);
                }

                graficar(labels, values, (parseInt(valor_mas_alto) * 1.05), 1000, '$', $('#sales-chart'), 'line');
            } else {
                $('#sales-chart').html('<div class="alert alert-warning">No existe datos para el grafico. </div>');
            }
        },
        complete: function () {
            if(!isError) $('#loader_ventas_por_mes').hide();
        }
    });
}

function cargar_grafica_ventasXmesBULTOS() {
    let isError = false;
    $.ajax({
        cache: false,
        async: false,
        url: "principal/principal_controlador.php?op=buscar_ventasPormesbultos",
        type: "post",
        dataType: "json",
        beforeSend: function () {
            $('#loader_ventas_por_mes_dos').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if (!jQuery.isEmptyObject(data)) {
                let { anio, cantidad_meses_evaluar, datos, valor_mas_alto } = data;

                $('#title_ventas_dos').text('del año ' + anio);

                let labels = [], values = [];
                if (!jQuery.isEmptyObject(datos)) {
                    //titulos de las barras
                    labels = datos[0].ventas_ano_actual.map(val => { return val.mes; });

                    //acumulado de ventas
                    $('#acum_ventas_anio_actual_dos')
                        .html(`${sum(datos[0].ventas_ano_actual.map(val => { return parseFloat(val.valor); }))
                            .format_money( '.', ',')}</span><sup style="font-size: 18px">Bultos</sup>`
                        );

                    //simbolizacion ventas desde mes pasado
                    const porcentaje = incremento_porcentual_ventas(datos[0].ventas_ano_actual);
                    $('.incremento_ventas_dos').removeClass('text-success').addClass((porcentaje >= 0) ? 'text-success' : 'text-danger')
                    $('.incremento_ventas_dos').html(`<i class="fas fa-arrow-${(porcentaje >= 0) ? 'up' : 'dowm'}"></i> ${porcentaje.format_money(2, 3, '.', ',')} %`);

                    //valores de las barras
                    values[0] = get_values(datos[0].ventas_ano_actual, cantidad_meses_evaluar);
                    values[1] = get_values(datos[1].ventas_ano_anterior, cantidad_meses_evaluar);
                }

                graficar(labels, values, (parseInt(valor_mas_alto) * 1.05), 1000, '', $('#sales-chart_dos'), 'line');
            } else {
                $('#sales-chart_dos').html('<div class="alert alert-warning">No existe datos para el grafico. </div>');
            }
        },
        complete: function () {
            if (!isError) $('#loader_ventas_por_mes_dos').hide();
        }
    });
}

function fetch_inventario_valorizado() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=listar_inventario_valorizado",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_inventario_valorizado').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { contenido_tabla } = data;
                $.each(contenido_tabla, function (idx, opt) {
                    $('#inventario_valorizado')
                        .append(
                            '<tr>' +
                                '<td class="align-middle">' + opt.almacen + '</td>' +
                                '<td class="align-middle">' + opt.total + ' $</td>' +
                                '<td class="align-middle">' + opt.acciones + '</td>' +
                            '</tr>'
                        );
                });
            } else {
                //en caso de consulta vacia, mostramos un mensaje de vacio
                $('#inventario_valorizado').append('<tr><td colspan="3" align="center">Sin registros para esta Consulta</td></tr>');
            }
        },
        complete: function () {
            if(!isError) $('#loader_inventario_valorizado').hide();
        }
    });
}


function modalVerDetalleAlmacen(correlativo) {
    if (correlativo !== "") {
        let isError = false;
        $("#ver_detalle_almacen").val(correlativo);

        var almacen = '';
        if (correlativo == '01') {
            almacen = 'Principal';
        } else {
            if (correlativo == '02') {
                almacen = 'Faltante';
            } else {
                if (correlativo == '06') {
                    $almacen = 'Dañado / Vencido';
                } else {
                    if (correlativo == '07') {
                        almacen = 'Mala Calidad Parmalat';

                    } else {
                        if (correlativo == '08') {
                            almacen = 'Dev Proveedor';
                        } else {
                            if (correlativo == '14') {
                                almacen = 'Muestra Dev';
                            } else {
                                if (correlativo == '100') {
                                    almacen = 'Devolucion Proveedor';
                                }
                            }
                        }
                    }
                }
            }
        }
        $("#detalle_almacen").text(almacen);
        $('#verDetalleDealmacenModal').modal('show');

        $.ajax({
            url: "principal/principal_controlador.php?op=buscar_detalles_almacenes",
            method: "post",
            dataType: "json",
            data: { correlativo:correlativo },
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {

                //TABLA DE LAS FACTURAS DENTRO DE ESE DESPACHO
                $('#tabla_detalle_almacen').dataTable({
                    "aProcessing": true,//Activamos el procesamiento del datatables
                    "aServerSide": true,//Paginación y filtrado realizados por el servidor

                    "sEcho": data.tabla.sEcho, //INFORMACION PARA EL DATATABLE
                    "iTotalRecords": data.tabla.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
                    "iTotalDisplayRecords": data.tabla.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
                    "aaData": data.tabla.aaData, // informacion por registro

                    "bDestroy": true,
                    "responsive": true,
                    "bInfo": true,
                    "iDisplayLength": 10,//Por cada 10 registros hace una paginación
                    "order": [[0, "asc"]],//Ordenar (columna,orden)
                    'columnDefs': [{
                        "targets": 3, // your case first column
                        "className": "text-center",
                    }],
                    "language": texto_español_datatables
                }).DataTable();

                $("#total_cantBul_tfoot").text(data.cantidad_b);
                $("#total_cantPaq_tfoot").text(data.cantidad_p);
                $("#cantBul_tfoot").text(data.total_b);
                $("#cantPaq_tfoot").text(data.total_p);
                $("#cantValor_tfoot").text(data.total);
                $("#loader_detalle_productos_despacho").hide();//OCULTAMOS EL LOADER.
            },
            complete: function () {
                if (!isError) SweetAlertLoadingClose();
            }
        });
    }
}

function fetch_clientes() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_clientes",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_clientes').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { cant_naturales, cant_juridico } = data;
                $('#clientes').text(cant_naturales + ' / ' + cant_juridico);
            }
        },
        complete: function () {
            if(!isError) $('#loader_clientes').hide();
        }
    });
}

function fetch_total_ventas_mes_encurso() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_total_ventas_mes_encurso",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_total_ventas_mes_encurso').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { total, fecha } = data;
                $('#ventas_mes_encurso').html(total + '<sup style="font-size: 16px">$</sup>');
                $('#ventas_mes_text').text(fecha);
            }
        },
        complete: function () {
            if(!isError) $('#loader_total_ventas_mes_encurso').hide();
        }
    });
}

function fetch_tasa_dolar() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_tasa_dolar",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_tasa_dolar').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { tasa } = data;
                $('#tasa_dolar').html(tasa + '<sup style="font-size: 16px">BS</sup>');
            }
        },
        complete: function () {
            if(!isError) $('#loader_tasa_dolar').hide();
        }
    });
}

function fetch_devoluciones_sin_motivo() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=buscar_devoluciones_sin_motivo",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_devoluciones_sin_motivo').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { devoluciones_sin_motivo } = data;
                $('#devoluciones_sin_motivo').text(devoluciones_sin_motivo);
            }
        },
        complete: function () {
            if(!isError) $('#loader_devoluciones_sin_motivo').hide();
        }
    });
}

function fetch_top_marcas() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=listar_ventas_por_marca",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_ventas_por_marca').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { fecha, marcas } = data;

                $('#title_ventas_marca').text(fecha);

                $.each(marcas, function (idx, opt) {
                    $('#ventas_por_marca')
                        .append(
                            '<tr>' +
                            '<td class="align-middle">' + idx + '</td>' +
                            '<td class="align-middle">' + opt.format_money(2, 3, '.', ',') + ' $</td>' +
                            '</tr>'
                        );
                });
            } else {
                //en caso de consulta vacia, mostramos un mensaje de vacio
                $('#ventas_por_marca').append('<tr><td colspan="2" align="center">Sin registros para esta Consulta</td></tr>');
            }
        },
        complete: function () {
            if(!isError) $('#loader_ventas_por_marca').hide();
        }
    });
}

function fetch_top_clientes() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=listar_ventas_por_clientes",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_top_clientes').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)){
                let { fecha, clientes } = data;

                $('#title_top_clientes').text(fecha);

                $.each(clientes, function (idx, opt) {
                    $('#top_clientes')
                        .append(
                            '<tr>' +
                            '<td class="text-left">' + opt.descrip + '</td>' +
                            '<td class="align-middle">' + opt.montod.format_money(2, 3, '.', ',') + ' $</td>' +
                            '</tr>'
                        );
                });
            } else {
                //en caso de consulta vacia, mostramos un mensaje de vacio
                $('#top_clientes').append('<tr><td colspan="2" align="center">Sin registros para esta Consulta</td></tr>');
            }
        },
        complete: function () {
            if(!isError) $('#loader_top_clientes').hide();
        }
    });
}


function fetch_top_productos() {
    let isError = false;
    $.ajax({
        cache: true,
        url: "principal/principal_controlador.php?op=listar_ventas_por_productos",
        method: "get",
        dataType: "json",
        beforeSend: function () {
            $('#loader_ventas_por_productos').show()
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if (!jQuery.isEmptyObject(data)) {
                let { fecha, marcas } = data;

                $('#title_ventas_productos').text(fecha);

                $.each(marcas, function (idx, opt) {
                    $('#ventas_por_productos')
                        .append(
                            '<tr>' +
                            '<td class="align-middle">' + idx + '</td>' +
                            '<td class="align-middle">' + opt.format_money(2, 3, '.', ',') + ' $</td>' +
                            '</tr>'
                        );
                });
            } else {
                //en caso de consulta vacia, mostramos un mensaje de vacio
                $('#ventas_por_productos').append('<tr><td colspan="2" align="center">Sin registros para esta Consulta</td></tr>');
            }
        },
        complete: function () {
            if (!isError) $('#loader_ventas_por_productos').hide();
        }
    });
}


init();