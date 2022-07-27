let estado_minimizado;

// 1-efectivas  2-rechazo  3-oportunidad
let indicador_seleccionado;
let causas_rechazo;

let array_selects = [
    'pills-efectivas',
    'pills-rechazo',
    'pills-oportunidad'
];

let barChart;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    $('#grafico').hide();
    estado_minimizado = false;
    indicador_seleccionado = 1;
    causas_rechazo = {};
    listar_choferes();
    listar_causas_rechazo();
    limpiar();
    switch (indicador_seleccionado) {
        case 1: $("#pills-fectivas-tab").trigger("click");    break;
        case 2: $("#pills-rechazo-tab").trigger("click");     break;
        case 3: $("#pills-oportunidad-tab").trigger("click"); break;
    }

}

function limpiar() {
    array_selects.forEach( pill => {
        $('#'+pill+' #chofer').val("");
        $('#'+pill+' #causa').val("");
        $('#'+pill+' #tipoPeriodo').val("");
        $('#'+pill+' #periodo').val("");
        $('#'+pill+' #tipoPeriodo').prop("disabled", true);
        $('#'+pill+' #periodo').prop("disabled", true);
        $('#'+pill+' #causa').prop("disabled", true);
    });
    $('[name="ped_pendiente"]').show();
    $('[name="diario_despachos"]').show();
    $('#ordenes_label').show();
    $('[name="ttl_ped_camion"]').show();
    $('#ordenes_despacho').text("");
    $('#fact_sinliquidar').text("");
}

function limpiar_grafico() {
    if (barChart) {
        barChart.clear();
        barChart.destroy();
    }
}

function validarCantidadRegistrosTabla(data) {
    (data.length === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

let no_puede_estar_vacio = function () {
    let pill = array_selects[indicador_seleccionado - 1];

    ($("#" + pill + " #tipoPeriodo").val() !== "" && $("#" + pill + " #periodo").val() !== "" && $("#" + pill + " #chofer").val() !== "" &&
        (indicador_seleccionado !== 2 || (indicador_seleccionado === 2 && $("#" + pill + " #causa").val() !== "")))
        ? estado_minimizado = true : estado_minimizado = false;
};

let es_habilidado = function () {
    let pill = array_selects[indicador_seleccionado - 1];

    if($("#" + pill + " #chofer").val() !== "") {
        $("#" + pill + " #tipoPeriodo").prop("disabled", false);
    } else {
        $('#' + pill + ' #tipoPeriodo').val("");
        $('#' + pill + ' #periodo').val("");
        $("#" + pill + " #tipoPeriodo").prop("disabled", true);
        $("#" + pill + " #periodo").prop("disabled", true);
    }

    if($("#" + pill + " #tipoPeriodo").val() !== "") {
        $('#'+pill+' #periodo').empty();
        $('#'+pill+' #periodo').append('<option>cargando...</option>');
        listar_periodos();
    } else {
        $('#'+pill+' #periodo').empty();
        $("#" + pill + " #periodo").prop("disabled", true);
    }
}

$(document).ready(function(){
    array_selects.forEach( pill => {
        $("#"+pill+" #periodo").change(() => {
            no_puede_estar_vacio();
            if($("#" + pill + " #periodo").val() !== "") {
                $("#" + pill + " #causa").prop("disabled", false);
            } else {
                $("#" + pill + " #causa").prop("disabled", true);
            }
        });
        $("#"+pill+" #chofer").change(() => { 
            no_puede_estar_vacio(); 
            es_habilidado();
        });
        $("#"+pill+" #tipoPeriodo").change(() => {
            no_puede_estar_vacio();
            es_habilidado();
        });
        $("#"+pill+" #causa").change(() => no_puede_estar_vacio());
    });

    $("#pills-fectivas-tab").on("click", function (e) {
        indicador_seleccionado = 1;
    });
    $("#pills-rechazo-tab").on("click", function (e) {
        indicador_seleccionado = 2;
    });
    $("#pills-oportunidad-tab").on("click", function (e) {
        indicador_seleccionado = 3;
    });
});

function listar_choferes(){
    let isError = false;
    $.ajax({
        url: "indicadoresdespacho_controlador.php?op=listar_choferes",
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
            array_selects.forEach( pill => {
                $chofer = $('#'+pill+' #chofer');

                //lista de seleccion de choferes
                $chofer.append('<option name="" value="">Seleccione chofer</option>');
                if(pill === 'pills-rechazo')
                    $chofer.append('<option name="" value="-">Todos</option>');
                $.each(data.lista_choferes, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $chofer.append('<option name="" value="' + opt.cedula +'">' + opt.descripcion + '</option>');
                });
            });
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function listar_causas_rechazo(){
    $.ajax({
        url: "indicadoresdespacho_controlador.php?op=obtener_causas_rechazo",
        method: "POST",
        dataType: "json",
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            causas_rechazo = data.lista_causas;

            $causa = $('#pills-rechazo #causa');

            //lista de seleccion de causas
            $causa.append('<option name="" value="">--Seleccione Causa del rechazo--</option>');
            $causa.append('<option name="" value="todos">Todos</option>');
            $.each(data.lista_causas, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $causa.append('<option name="" value="' + opt.id +'">' + opt.descripcion + '</option>');
            });
        }
    });
}

function listar_periodos(){
    let pill = array_selects[indicador_seleccionado - 1];

    let tipoPeriodo = $("#"+pill+" #tipoPeriodo").val();
    let chofer_id = $("#"+pill+" #chofer").val();

    $.ajax({
        url: "indicadoresdespacho_controlador.php?op=listar_periodos&s="+indicador_seleccionado,
        method: "POST",
        dataType: "json",
        data: {tipoPeriodo: tipoPeriodo, chofer_id: chofer_id},
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {

            $('#'+pill+' #periodo').empty();

            if(data.error) {
                $('#'+pill+' #periodo').append('<option name="" value="">' + data.error + '</option>');
            }
            else {
                $('#'+pill+' #periodo').prop("disabled", false);
                //lista de seleccion de periodos
                $('#'+pill+' #periodo').append('<option name="" value="">Seleccione periodo</option>');
                $.each(data, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#'+pill+' #periodo').append('<option name="" value="' + opt.value +'">' + opt.label + '</option>');
                });
            }
        }
    });
}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    let formData;
    let listar;
    let title;

    switch (indicador_seleccionado) {
        case 1:
            listar = "listar_entregas_efectivas";
            title = $("#pills-fectivas-tab").text().trim();
            formData = $("#efectivas_form").serializeArray();
            break;
        case 2:
            listar = "listar_causas_rechazo";
            title = $("#pills-rechazo-tab").text().trim();
            formData = $("#rechazo_form").serializeArray();
            break;
        case 3:
            listar = "listar_oportunidad_despacho";
            title = $("#pills-oportunidad-tab").text().trim();
            formData = $("#oportunidad_form").serializeArray();
            break;
    }

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#grafico").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if ((formData[0]['name'] === "chofer" && formData[0]['value'] !== "")
            && (formData[1]['name'] === "tipoPeriodo" && formData[1]['value'] !== "")
            && (formData[2]['name'] === "periodo" && formData[2]['value'] !== "")
            && (indicador_seleccionado!==2 || (indicador_seleccionado===2 && formData[3]['name'] === "causa" && formData[3]['value'] !== ""))
        ) {
            sesionStorageItems(
                formData[0]['value'],
                formData[1]['value'],
                formData[2]['value'],
                (indicador_seleccionado===2) ? formData[3]['value'] : ""
            );

            //reordenamos el array serializado para volverlo array asociativo.
            let form_data = {};
            formData.forEach( val => { form_data[val.name] = val.value } );

            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            $.ajax({
                url: "indicadoresdespacho_controlador.php?op="+listar,
                method: "POST",
                dataType: "json",
                data: form_data,
                beforeSend: function () {
                    limpiar_grafico();
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    $(".title-card").text(title);
                    limpiar();//LIMPIAMOS EL SELECTOR.
                    
                    $("#tabla").show('');//MOSTRAMOS LA TABLA.
                    $("#grafico").show('');//MOSTRAMOS EL GRAFICO.

                    //limpiamos la tabla
                    $('#indicadores_data tbody').empty();
                    condicion_tipoperiodo = formData[1]['value'] === "Anual";

                    //llenamos inputs date disabled
                    $("#fechai_disabled").val(data.fechai);
                    $("#fechaf_disabled").val(data.fechaf);

                    //proceso de llenado de la tabla
                    construirTabla(data.tabla, condicion_tipoperiodo);

                    //llenado de los span
                    llenadoDeSpan(data);

                    //proceso de llenado del grafico
                    construirGrafico(data, condicion_tipoperiodo);

                    validarCantidadRegistrosTabla(data.tabla);
                },
                complete: function () {
                    if(!isError) SweetAlertLoadingClose();
                }
            });

            estado_minimizado = true;
        }
    } else {
        if (formData[0]['name'] === "chofer" && formData[0]['value'] === "") {
            SweetAlertError('Seleccione un chofer!');
            return (false);
        }
        if (formData[1]['name'] === "tipoPeriodo" && formData[1]['value'] === "") {
            SweetAlertError('Seleccione un Tipo de Periodo!');
            return (false);
        }
        if (formData[2]['name'] === "periodo" && formData[2]['value'] === "") {
            SweetAlertError('Seleccione un Periodo!');
            return (false);
        }
        if (indicador_seleccionado===2 && formData[3]['name'] === "causa" && formData[3]['value'] === "") {
            SweetAlertError('Seleccione una causa de rechazo!');
            return (false);
        }
    }
});

function construirGrafico(data, condicion_visibilidad_mes) {
    let object, value_max_default;

    switch (indicador_seleccionado) {
        case 1:
            object = entregas_efectivas(data, condicion_visibilidad_mes);
            value_max_default = 25;
            break;
        case 2:
            object = rechazo_de_los_clientes(data, condicion_visibilidad_mes, causas_rechazo);
            value_max_default = 8;
            break;
        case 3:
            object = oportunidad_despacho(data, condicion_visibilidad_mes);
            value_max_default = 8;
            break;
    }

    //CONSTRUCCION DEL GRAFICO
    var barChartCanvas = $('#barChart').get(0).getContext('2d')
    barChart = new Chart(barChartCanvas, {
        type: 'bar',
        data: jQuery.extend(true, {}, {
            labels  : object.labels,
            datasets: []
        }),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            datasetFill: false,
            tooltips: {
                mode: 'label'
            },
            scales: {
                yAxes: [{
                    ticks: {
                        suggestedMin: 0,
                        suggestedMax: (object.value_max >= 25) ? object.value_max+5 : value_max_default
                    }
                }],
            },
        }
    });

    object.content.forEach( val => {
        const {label, type, color, pointRadius, fill, values} = val;
        barChart.data.datasets.push({
            label               : label,
            type                : type,
            backgroundColor     : color,
            borderColor         : color,
            pointRadius         : pointRadius,
            pointStrokeColor    : color,
            pointHighlightFill  : '#fff',
            pointHighlightStroke: color,
            fill                : fill,
            data                : values
        });

    });

    barChart.update();
}

function construirTabla(data, incluye_ordenes){

    $('#indicadores_data thead').empty();

    switch(indicador_seleccionado){
        case 1: $('#indicadores_data thead').append( thead_table_efectivas(incluye_ordenes) ); break;
        case 2: $('#indicadores_data thead').append( thead_table_rechazo(incluye_ordenes) ); break;
        case 3: $('#indicadores_data thead').append( thead_table_oportunidad() ); break;
    }

    if(!jQuery.isEmptyObject(data)) {

        if(indicador_seleccionado===1 || indicador_seleccionado===2) {
            $.each(data, function(idx, opt) {
                ordenes = (!incluye_ordenes) ? '<td align="center" class="small align-middle">' + opt.ordenes_despacho + '</td>' : '';

                $('#indicadores_data')
                    .append(
                        '<tr>' +
                        '<td align="center" class="small align-middle">' + opt.fecha_entrega + '</td>' +
                        '<td align="center" class="small align-middle">' + opt.cant_documentos + '</td>' +
                        '<td align="center" class="small align-middle">' +
                            '<div class="progress progress-sm"> ' +
                            '<div class="progress-bar bg-green" role="progressbar" aria-volumenow="'+ parseInt(opt.porc) +'" aria-volumemin="0" aria-volumemax="100" style="width: '+ parseInt(opt.porc) +'%"></div> ' +
                            '</div> ' +
                            '<small>' + parseInt(opt.porc * 10) / 10 + ' %</small>' +
                        '</td>' +
                        ordenes +
                        '</tr>'
                    );
            });
        } else if (indicador_seleccionado===3) {
             $.each(data, function(idx, opt) {
                $('#indicadores_data')
                    .append(
                        '<tr>' +
                        '<td align="center" class="small align-middle">' + opt.fecha_desp + '</td>' +
                        '<td align="center" class="small align-middle">' + opt.cant_documentos + '</td>' +
                        '<td align="center" class="small align-middle"> ' +
                            '<div class="progress progress-sm"> ' +
                                '<div class="progress-bar bg-green" role="progressbar" aria-volumenow="'+ parseInt(opt.oportunidad) +'" aria-volumemin="0" aria-volumemax="100" style="width: '+ parseInt(opt.oportunidad) +'%"></div> ' +
                            '</div> ' +
                            '<small>' + parseInt(opt.oportunidad * 10) / 10 + ' %</small>' +
                        '</td>' +
                        '</tr>'
                    );
            });
        }
    } else {
        cols = indicador_seleccionado===3 ? 4 : 6;
        //en caso de consulta vacia, mostramos un mensaje de vacio
        $('#indicadores_data').append('<tr><td colspan="'+cols+'" align="center">Sin registros para esta Consulta</td></tr>');
    }
}

function llenadoDeSpan(data){
    $fact = $("#fact_label");
    let totalDespacho;
    let pedporliquidar;
    let total_ped;
    let promediodiario;

    if(!jQuery.isEmptyObject(data)) {
        if(indicador_seleccionado===1 || indicador_seleccionado===2) {
            totalDespacho = data.totaldespacho;
            total_ped = data.total_ped;
            if(indicador_seleccionado===1){
                pedporliquidar = data.total_ped_porliquidar;
                promediodiario = data.promedio_diario_despacho;
            }
        }
        if(indicador_seleccionado===3){
            total_ped = data.total_ped;
            promediodiario = data.oportunidad_promedio;
        }
    } else {
        totalDespacho   = 0;
        total_ped       = 0;
        pedporliquidar  = 0;
        promediodiario  = 0;
    }

    $("#datos_chofer").val(data.chofer);

    switch (indicador_seleccionado) {
        case 1:
            $("#label_tipo").text('entregados');
            $("#total_ped_camion").text(totalDespacho);
            $("#total_ped_entregados").text(total_ped);
            $("#total_ped_pendiente").text(pedporliquidar);
            $("#promedio_diario_despachos").text(promediodiario);
            $("#ordenes_despacho").text(
                data.ordenes_despacho.length > 0 ?
                    data.ordenes_despacho.substr(0, data.ordenes_despacho.length-2) : "Sin registros para esta Consulta"
            );
            $fact.text('FACTURAS SIN LIQUIDAR');
            $("#fact_sinliquidar").text(data.fact_sinliquidar.substr(1, data.fact_sinliquidar.length));
            break;
        case 2:
            $("#label_tipo").text('devueltos');
            $("#total_ped_camion").text(totalDespacho);
            $("#total_ped_entregados").text(total_ped);
            $('[name="ped_pendiente"]').hide();
            $('[name="diario_despachos"]').hide();
            $("#ordenes_despacho").text(
                data.ordenes_despacho.length > 0 ?
                    data.ordenes_despacho.substr(0, data.ordenes_despacho.length-2) : "Sin registros para esta Consulta"
            );
            $fact.text('CAUSA DEL RECHAZO');
            $("#fact_sinliquidar").text(sessionStorage.getItem("causa"));
            break;
        case 3:
            $("#label_tipo").text('');
            $("#total_ped_entregados").text(total_ped);
            $('#ordenes_label').hide();
            $('[name="ttl_ped_camion"]').hide();
            $('[name="ped_pendiente"]').hide();
            $('[name="diario_despachos"]').hide();
            $fact.text('DOCUMENTOS');
            $("#fact_sinliquidar").text(
                data.ordenes_despacho.length > 0 ?
                    data.ordenes_despacho.substr(0, data.ordenes_despacho.length-2) : "Sin registros para esta Consulta"
            );
            break;
    }

}

function sesionStorageItems(chofer, tipoPeriodo, periodo, causa = ""){
    sessionStorage.setItem("chofer", chofer);
    sessionStorage.setItem("tipoPeriodo", tipoPeriodo);
    sessionStorage.setItem("periodo", periodo);
    sessionStorage.setItem("causa", causa);
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    const chofer = sessionStorage.getItem("chofer");
    const tipoPeriodo = sessionStorage.getItem("tipoPeriodo");
    const periodo = sessionStorage.getItem("periodo");
    const causa  = sessionStorage.getItem("causa");
    
    if (tipoPeriodo !== "" && periodo !== "" && chofer !== "") {
        switch (indicador_seleccionado) {
            case 1:
                window.location = "reporte_entregas_efectivas_excel.php?tipoPeriodo="+tipoPeriodo+"&periodo="+periodo+"&chofer="+chofer;
                break;
            case 2:
                window.location = "reporte_rechazo_clientes_excel.php?tipoPeriodo="+tipoPeriodo+"&periodo="+periodo+"&chofer="+chofer+"&causa="+causa;
                break;
            case 3:
                window.location = "reporte_oportunidad_despacho_excel.php?tipoPeriodo="+tipoPeriodo+"&periodo="+periodo+"&chofer="+chofer;
                break;
        }
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    const chofer = sessionStorage.getItem("chofer");
    const tipoPeriodo = sessionStorage.getItem("tipoPeriodo");
    const periodo = sessionStorage.getItem("periodo");
    const causa  = sessionStorage.getItem("causa");

    if (tipoPeriodo !== "" && periodo !== "" && chofer !== "") {
        switch (indicador_seleccionado) {
            case 1:
                window.open('reporte_entregas_efectivas_pdf.php?&tipoPeriodo='+tipoPeriodo+"&periodo="+periodo+"&chofer="+chofer, '_blank');
                break;
            case 2:
                window.open('reporte_rechazo_clientes_pdf.php?&tipoPeriodo='+tipoPeriodo+"&periodo="+periodo+"&chofer="+chofer+"&causa="+causa, '_blank');
                break;
            case 3:
                window.open('reporte_oportunidad_despacho_pdf.php?&tipoPeriodo='+tipoPeriodo+"&periodo="+periodo+"&chofer="+chofer, '_blank');
                break;
        }
    }
});

init();
