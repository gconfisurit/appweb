
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listar_libroventas();
}

$(document).ready(function(){

});

function listar_libroventas(){

    let fechai    = $('#fechai').val();
    let fechaf    = $('#fechaf').val();

    $.ajax({
        url: "libroventas_controlador.php?op=listar_libroventas",
        method: "POST",
        data: {fechai: fechai, fechaf: fechaf},
        dataType: "json", // Formato de datos que se espera en la respuesta
        beforeSend: function () {
            SweetAlertLoadingShow("Procesando información, espere...");
        },
        error: function (e) {
            SweetAlertError(e.responseText.substring(0, 400) + "...", "Error!")
            console.log(e.responseText);
        },
        success: function (datos) {

            tablalibroventas(datos);
            tablaliretencionesotrosperiodos(datos);
            tablaresumen(datos);

        },
        complete: function () {
            SweetAlertSuccessLoading()
            const arr = ['#tabla','#tabla1','#tabla2'];

            arr.forEach(val => {
                $(val).columntoggle({
                    //Class of column toggle contains toggle link
                    toggleContainerClass:'columntoggle-container',
                    //Text in column toggle box
                    toggleLabel:'MOSTRAR/OCULTAR CELDAS: ',
                    //the prefix of key in localstorage
                    keyPrefix:'columntoggle-',
                    //keyname in localstorage, if empty, it will get from URL
                    key:''

                });
            });
        }
    });
}

function tablalibroventas(data) {
    let { tabla, totales_libro } = data;

    if (!jQuery.isEmptyObject(tabla))
    {
        $.each(tabla, function(idx, opt) {
            $('#tabla').append(
                '<tr>' +
                '<td align="center" class="small align-middle">' + opt.num + '</td>' +
                '<td align="center" class="small align-middle">' + opt.fechaemision + '</td>' +
                '<td align="center" class="small align-middle">' + opt.rifcliente + '</td>' +
                '<td align="center" class="small text-left">' + opt.nombre + '</td>' +
                '<td align="center" class="small align-middle">' + opt.tipodoc + '</td>' +
                '<td align="center" class="small align-middle">' + opt.numerodoc + '</td>' +
                '<td align="center" class="small align-middle">' + opt.nroctrol + '</td>' +
                '<td align="center" class="small align-middle">' + opt.tiporeg + '</td>' +
                '<td align="center" class="small align-middle">' + opt.factafectada + '</td>' +
                '<td align="center" class="small align-middle">' + opt.nroretencion + '</td>' +
                '<td align="center" class="small text-right">' + opt.totalventasconiva + '</td>' +
                '<td align="center" class="small text-right">' + opt.mtoexento + '</td>' +
                '<td align="center" class="small text-right">' + opt.base_imponible + '</td>' +
                '<td align="center" class="small align-middle">' + opt.alicuota_contribuyeiva+ '%</td>' +
                '<td align="center" class="small text-right">' + opt.montoiva_contribuyeiva + '</td>' +
                '<td align="center" class="small align-middle">' + opt.retencioniva + '</td>' +
                '</tr>'
            );
        });
    }

    bold  = 'style="font-weight: bold"';
    $('#tabla').append('<tr><td colspan="18">'+ "" +'</td></tr>');
    $('#tabla').append(
        '<tr>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'>Totales</td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales_libro.tvii + '</td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales_libro.ve + '</td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales_libro.magbi16c + '</td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales_libro.mag16c + '</td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales_libro.ivare + '</td>' +
        '</tr>'
    );
}

function tablaliretencionesotrosperiodos(data) {
    let { otros_periodos, totales_otros_periodos } = data;

    if (!jQuery.isEmptyObject(otros_periodos))
    {
        $.each(otros_periodos, function(idx, opt) {
            $('#tabla1').append(
                '<tr>' +
                '<td align="center" class="small align-middle">' + opt.num + '</td>' +
                '<td align="center" class="small align-middle">' + opt.fechaemision + '</td>' +
                '<td align="center" class="small align-middle">' + opt.rifcliente + '</td>' +
                '<td align="center" class="small text-left">' + opt.nombre + '</td>' +
                '<td align="center" class="small align-middle">' + opt.tipodoc + '</td>' +
                '<td align="center" class="small align-middle">' + opt.numerodoc + '</td>' +
                '<td align="center" class="small align-middle">' + opt.tiporeg + '</td>' +
                '<td align="center" class="small align-middle">' + opt.factafectada + '</td>' +
                '<td align="center" class="small align-middle">' + opt.fecharetencion + '</td>' +
                '<td align="center" class="small align-middle">' + opt.totalgravable_contribuye + '</td>' +
                '<td align="center" class="small text-right">' + opt.totalivacontribuye + '%</td>' +
                '<td align="center" class="small text-right">' + opt.retencioniva + '</td>' +
                '</tr>'
            );
        });
    }

    bold  = 'style="font-weight: bold"';
    $('#tabla1').append('<tr><td colspan="12">'+ "" +'</td></tr>');
    $('#tabla1').append(
        '<tr>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'>Totales</td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales_otros_periodos.ivare + '</td>' +
        '</tr>'
    );
}

function tablaresumen(data) {
    let { resumen } = data;

    if (!jQuery.isEmptyObject(resumen))
    {
        $.each(resumen, function(idx, opt) {
            let { isBold, isColored } = opt;
            isBold  = (isBold) ? 'style="font-weight: bold"' : "";

            $('#tabla2').append(
                '<tr>' +
                    '<td align="center" class="small text-left '+(isColored?'bg-secondary':'')+'" '+isBold+'>' + opt.descripcion + '</td>' +
                    '<td align="center" class="small text-right '+(isColored?'bg-secondary':'')+'" '+isBold+'>' + opt.base_imponible + '</td>' +
                    '<td align="center" class="small text-right '+(isColored?'bg-secondary':'')+'" '+isBold+'>' + opt.credito_fiscal + '</td>' +
                '</tr>'
            );
        });
    }
}

init();