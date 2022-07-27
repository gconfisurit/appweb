var tabla_relacion_despachos;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listarRelacionDespachos();

    $("#modalMostrarEditarDespacho").on("click", function (e) {
        var correlativo = $('#correlativo').val();
        modalMostrarEditarDespacho(correlativo);
    });

}

function limpiar_campo_documento_modal() {
    $("#nrodocumento").val("");
    $("#detalle_despacho").html("");
    $("#detalle_despacho_liquidacion").html("");
}

function limpiar_modal_detalle_despacho() {
    $('#tabla_editar_despacho tbody').empty();
    $("#correlativo").val("");
    $("#correl").text("");
    $("#Destino").text("");
    $("#fechad").text("");
    $("#vehiculo").text("");
    $("#cantFacturas").text("");
}

function modalEditarDespachos(correlativo) { //editar
    let isError = false;
    limpiar_modal_detalle_despacho();
    if (correlativo !== "") {
        $.ajax({
            url: "despachosrelacion_controlador.php?op=buscar_despacho_por_correlativo",
            method: "POST",
            dataType: "json",
            data: { correlativo: correlativo },
            beforeSend: function () {
                $('#editarDespachoModal').modal('show');
                $('#relacion_despacho_editar').hide();
                $('#modalMostrarEditarDespacho').hide();
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                //CABECERA DEL DESPACHO
                $("#correlativo").val(correlativo);
                $("#correl").text(data.correl);
                $("#Destino").text(data.destino);
                $("#fechad").text(data.fechad);
                $("#vehiculo").text(data.vehiculo);
                $("#cantDocumentos").text(data.cantDocumentos);
                $('#modalMostrarEditarDespacho').show();

                $('#relacion_despacho_editar').show();
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });

        $('#tabla_editar_despacho').dataTable({
            "aProcessing": true,//Activamos el procesamiento del datatables
            "aServerSide": true,//Paginación y filtrado realizados por el servidor
            "ajax":
                {
                    url: 'despachosrelacion_controlador.php?op=buscar_destalle_despacho_por_correlativo',
                    method: "POST",
                    dataType: "json",
                    data: { correlativo:correlativo },
                    error: function (e) {
                        SweetAlertError(e.responseText, "Error!")
                        send_notification_error(e.responseText);
                        console.log(e.responseText);
                    },
                },

            "bDestroy": true,
            "responsive": true,
            "bInfo": true,
            "iDisplayLength": 10,//Por cada 10 registros hace una paginación
            "order": [[0, "desc"]],//Ordenar (columna,orden)
            'columnDefs':[{
                "targets": 3, // your case first column
                "className": "text-center"
            }],
            "language": texto_español_datatables
        }).DataTable();
    }
}

function modalMostrarEditarDespacho(correlativo) {
    let isError = false;
    $('#alert_editar_despacho').hide();
    $.ajax({
        url: "despachosrelacion_controlador.php?op=buscar_cabeceraDespacho_para_editar",
        method: "POST",
        dataType: "json",
        data: {correlativo:correlativo},
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            //lista de seleccion de chofer
            $('#chofer_editar').html('');
            $('#chofer_editar').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_choferes, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#chofer_editar').append('<option name="" value="' + opt.cedula + '">'+ opt.descripcion + '</option>');
            });

            //lista de seleccion de vehiculos
            $('#vehiculo_editar').html('');
            $('#vehiculo_editar').append('<option name="" value="">Seleccione</option>');
            $.each(data.lista_vehiculos, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#vehiculo_editar').append('<option name="" value="' + opt.placa +'">' + opt.modelo + '  ' + opt.capacidad + ' Kg' + '</option>');
            });

            $("#destino_editar").val(data.destino);
            $("#fecha_editar").val(data.fecha);
            $("#chofer_editar").val(data.chofer);
            $("#vehiculo_editar").val(data.vehiculo);
            $("#correlativo_editar").val(correlativo);
            $('#editarChoferDestinoDespachoModal').modal('show');
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function modalGuardarEditarDespacho() {
    let isError = false;
    var destino = $("#destino_editar").val();
    var fechad = $("#fecha_editar").val();
    var chofer = $("#chofer_editar").val();
    var vehiculo = $("#vehiculo_editar").val();
    var correlativo = $("#correlativo_editar").val();

    if(destino.length > 0 && fechad.length > 0 && chofer.length > 0 && vehiculo.length >0){
        $('#editarChoferDestinoDespachoModal').modal('hide');
        $.ajax({
            url: "despachosrelacion_controlador.php?op=actualizar_cabeceraDespacho_para_editar",
            method: "POST",
            dataType: "json",
            data: {correlativo:correlativo, destino:destino, fechad:fechad, chofer:chofer, vehiculo:vehiculo},
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                if(!data.mensaje.includes('ERROR')){
                    modalEditarDespachos(correlativo);
                    $('#editarChoferDestinoDespachoModal').modal('hide');
                    $('#relacion_data').DataTable().ajax.reload();
                } else {
                    $('#alert_editar_despacho').show();
                }
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });
    } else {
        $('#alert_editar_despacho').show();
    }
}

function modalMostrarDocumentoEnDespacho(nro_documento, tipofac, correlativo) {
    $('#alert_editar_documento').hide();
    $("#documento_editar").val(addZeros(nro_documento));
    $("#viejo_documento_editar").val(addZeros(nro_documento));
    $("#viejo_tipofac_editar").val(tipofac);
    if (tipofac==='A') {
        $('#tipo_fact_modal_2').prop( "checked", true);
    } else if (tipofac==='C') {
        $('#tipo_not_modal_2').prop( "checked", true);
    }
    $("#correlativo_del_documento_editar").val(correlativo);
    $('#editarFacturaEnDespachoModal').modal('show');
}

function modalGuardarDocumentoEnDespacho() {
    let isError = false;
    const documento_nuevo = addZeros($("#documento_editar").val());
    const tipodoc_nuevo = $('input:radio[name=tipo_doc_modal_2]:checked').val()
    const documento_viejo = $("#viejo_documento_editar").val();
    const tipodoc_viejo = $("#viejo_tipofac_editar").val();
    const correlativo = $("#correlativo_del_documento_editar").val();


    if(documento_nuevo.length > 0 && documento_viejo.length > 0 && correlativo.length > 0){
        $.ajax({
            url: "despachosrelacion_controlador.php?op=actualizar_documento_en_despacho",
            method: "POST",
            dataType: "json",
            data: {
                correlativo: correlativo,
                documento_nuevo: documento_nuevo,
                documento_viejo: documento_viejo,
                tipodoc_nuevo: tipodoc_nuevo,
                tipodoc_viejo: tipodoc_viejo,
            },
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                if(!data.mensaje.includes('ATENCION!') && !data.mensaje.includes('ERROR')){
                    $('#editarFacturaEnDespachoModal').modal('hide');
                    $('#tabla_editar_despacho').DataTable().ajax.reload();
                    $('#relacion_data').DataTable().ajax.reload();
                } else {
                    $('#alert_editar_documento').show();
                    $('#text_alert_editar_documento').text(data.mensaje);
                }
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });
    } else {
        $('#alert_editar_documento').show();
    }
}

function modalEliminarDocumentoEnDespacho(nro_documento, tipodoc, correlativo) {

    Swal.fire({
        // title: '¿Estas Seguro?',
        text: "¿Estas Seguro de Eliminar el documento "+nro_documento+" ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            let isError = false;
            $.ajax({
                url: "despachosrelacion_controlador.php?op=eliminar_documento_en_despacho",
                method: "POST",
                dataType: "json",
                data: {
                    correlativo: correlativo,
                    nro_documento: nro_documento,
                    tipodoc: tipodoc,
                },
                beforeSend: function () {
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText, "Error!");
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    SweetAlertSuccessLoading(data.mensaje);
                    $('#tabla_editar_despacho').DataTable().ajax.reload();
                    $('#relacion_data').DataTable().ajax.reload();
                },
                complete: function () {
                    if(!isError) SweetAlertLoadingClose();
                }
            });
        }
    })
}

function modalAgregarDocumentoEnDespacho() {
    $('#alert_agregar_documento').hide();
    $('#agregarFacturaEnDespachoModal #documento_agregar').val("");
    $('#agregarFacturaEnDespachoModal').modal('show');
}

function modalGuardarNuevoDocumentoEnDespacho() {

    const correlativo = $("#correlativo").val();
    const documento_agregar = addZeros($("#documento_agregar").val());
    const tipodoc = $('input:radio[name=tipo_doc_modal_1]:checked').val()

    if(correlativo.length > 0 && documento_agregar.length > 0){
        let isError = false;
        $.ajax({
            url: "despachosrelacion_controlador.php?op=agregar_documento_en_despacho",
            method: "POST",
            dataType: "json",
            data: {
                correlativo: correlativo,
                documento_agregar: documento_agregar,
                tipodoc: tipodoc,
            },
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                if( data.cond === false ) {
                    //isError = true; // hubo un error
                    // SweetAlertError(data.mensaje);
                    $('#alert_agregar_documento').show();
                    $('#text_alert_agregar_documento').text(data.mensaje);
                } else {
                    $('#agregarFacturaEnDespachoModal').modal('hide');
                    $('#tabla_editar_despacho').DataTable().ajax.reload();
                    $('#relacion_data').DataTable().ajax.reload();
                }

                if(!data.mensaje.includes('ERROR')) {

                }
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });
    } else {
        $('#alert_agregar_documento').show();
    }
}


function EliminarUnDespacho(correlativo) {

    Swal.fire({
        // title: '¿Estas Seguro?',
        text: "¿Estas Seguro de Eliminar el despacho "+addZeros(correlativo, 8)+" ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "despachosrelacion_controlador.php?op=eliminar_un_despacho",
                method: "POST",
                dataType: "json",
                data: {correlativo: correlativo},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    ToastSweetMenssage(data.icono, data.mensaje)
                    //verifica si el mensaje de insercion no contiene error
                    if(!data.mensaje.includes('ERROR')) {
                        $('#relacion_data').DataTable().ajax.reload();
                    }
                }
            });
        }
    })
}

function modalVerDetalleDespacho(correlativo) {
    if (correlativo !== "") {
        let isError = false;
        $("#correlativo_ver_productos_despacho").val(correlativo);
        $("#nro_despacho").text(addZeros(correlativo, 8));
        $('#verDetalleDeUnDespachoModal').modal('show');

        $.ajax({
            url: "despachosrelacion_controlador.php?op=listar_productos_de_un_despacho",
            method: "post",
            dataType: "json",
            data: {correlativo: correlativo},
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
                $('#tabla_detalle_productos_del_despacho').dataTable({
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
                    'columnDefs':[{
                        "targets": 3, // your case first column
                        "className": "text-center",
                    }],
                    "language": texto_español_datatables
                }).DataTable();

                $("#cantBul_tfoot").text(data.total_bultos);
                $("#cantPaq_tfoot").text(data.total_paq);
                $("#loader_detalle_productos_despacho").hide();//OCULTAMOS EL LOADER.
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });
    }
}

function modalTipoReporte(correlativo) {
    $('#verTipoReporteModal').modal('show');

    $('#btnConsolidado').removeAttr('onclick');
    $('#btnConsolidado').attr('onClick', 'abrirReporteProductosDeUnDepacho('+correlativo+');');

    $('#btnDetallado').removeAttr('onclick');
    $('#btnDetallado').attr('onClick', 'abrirReporteDetalleCompletoDeUnDepacho('+correlativo+');');
}

function abrirReporteProductosDeUnDepacho(correlativo) {
    if (correlativo !== "") {
        window.open('../despachos/despachos_pdf.php?&correlativo=' + correlativo, '_blank');
    }
}

function abrirReporteDetalleCompletoDeUnDepacho(correlativo) {
    if (correlativo !== "") {
        window.open('despachosrelacion_detalle_pdf.php?&correlativo=' + correlativo, '_blank');
    }
}

function listarRelacionDespachos() {
    let isError = false;
    tabla_relacion_despachos = $('#relacion_data').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'despachosrelacion_controlador.php?op=listar_RelacionDespachos',
                type: "get",
                dataType: "json",
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
        "order": [[0, "desc"]],//Ordenar (columna,orden)
        'columnDefs':[{
                "targets": 3, // your case first column
                "className": "text-center",
                // "width": "4%"
        }],
        "language": texto_español_datatables
    }).DataTable();
}

function buscarDocumentoEnDespachos(nro_documento, tipodoc) {
    if (nro_documento !== "") {
        nro_documento = addZeros(nro_documento);
        $("#detalle_despacho").html("");
        $("#detalle_despacho_liquidacion").html("");
        let isError = false;
        $.ajax({
            url: "../despachos/despachos_controlador.php?op=buscar_documentoEnDespachos_modal",
            type: "POST",
            dataType: "json",
            data: {
                documento: nro_documento,
                tipodoc: tipodoc,
            },
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                if( !jQuery.isEmptyObject(data.mensaje) ) {
                    isError = true; // hubo un error
                    SweetAlertError(data.mensaje);
                } else {
                    if(!jQuery.isEmptyObject(data.documento_en_despacho)) {
                        let { numerod, tipofac, correlativo, fechae, destino } = data.documento_en_despacho;
                        $("#detalle_despacho").append(
                            '<p>' +
                            '<strong>Nro de Documento: </strong>  ' + numerod + ' <br>' +
                            '<strong>Tipo de Documento: </strong>  ' + tipofac + ' <br>' +
                            '<strong>Despacho Nro: </strong>  ' + correlativo + '<br>' +
                            '<strong>Fecha Emisión: </strong>  ' + fechae + '<br>' +
                            '<strong>Destino: </strong>  ' + destino +
                            '</p>'
                        );

                        if(!jQuery.isEmptyObject(data.datos_pago)){
                            $("#detalle_despacho_liquidacion").append(
                                '<p>' +
                                '<strong>PAGO: </strong>  ' + data.datos_pago.fecha_liqui +  '  ' +
                                '<strong>POR UN MONTO DE: </strong>  ' + data.datos_pago.monto_cancelado + ' BsS' +
                                '</p>'
                            );
                        }else{
                            $("#detalle_despacho_liquidacion").append('<br>DOCUMENTO NO LIQUIDADO');
                        }
                    } else {
                        $("#detalle_despacho_liquidacion").append('<br>EL DOCUMENTO INGRESADO <strong>NO A SIDO DESPACHADO</strong>');
                    }
                }
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });
    } else {
        $("#detalle_despacho").html("");
        $("#detalle_despacho_liquidacion").html("");
    }
}

//ACCION AL PRECIONAR EL BOTON BUSCAR.
$(document).on("click", "#btnBuscarDocModal", function () {
    const documento = $("#nrodocumento").val();
    const tipodoc = $('input:radio[name=tipo_doc_modal]:checked').val()
    buscarDocumentoEnDespachos(documento, tipodoc);
});

//ACCION AL PRECIONAR EL BOTON EXPORTAR A PDF DE DETALLE PRODUCTOS EN UN DESPACHO.
$(document).on("click", "#exportarDetalleDespacho_pdf", function () {
    abrirReporteProductosDeUnDepacho(
        $("#correlativo_ver_productos_despacho").val()
    );
});

//ACCION AL PRECIONAR EL BOTON EXPORTAR A PDF DE DETALLE PRODUCTOS EN UN DESPACHO.
$(document).on("click", "#DetalladoDespacho_pdf", function () {
    abrirReporteDetalleCompletoDeUnDepacho(
        $("#correlativo_ver_productos_despacho").val()
    );
});

init();