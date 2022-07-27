
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listar_librocompras();
}

$(document).ready(function(){

});

function listar_librocompras(){

    let fechai    = $('#fechai').val();
    let fechaf    = $('#fechaf').val();

    $.ajax({
        url: "librocompras_controlador.php?op=listar_librocompras",
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

            tablalibrocompras(datos);
            tablaresumen(datos);

        },
        complete: function () {
            SweetAlertSuccessLoading();
            const arr = ['#tabla','#tabla1'];

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

function tablalibrocompras(data) {
    let { tabla, totales } = data;

    if (!jQuery.isEmptyObject(tabla))
    {
        $.each(tabla, function(idx, opt) {
            $('#tabla').append(
                '<tr>' +
                '<td align="center" class="small align-middle">' + opt.num + '</td>' +
                '<td align="center" class="small align-middle">' + opt.fechacompra + '</td>' +
                '<td align="center" class="small align-middle">' + opt.id3ex + '</td>' +
                '<td align="center" class="small text-left">' + opt.descripex + '</td>' +
                '<td align="center" class="small align-middle">' + opt.tipodoc + '</td>' +
                '<td align="center" class="small align-middle">' + opt.nroretencion + '</td>' +
                '<td align="center" class="small align-middle">' + opt.numerodoc + '</td>' +
                '<td align="center" class="small align-middle">' + opt.nroctrol + '</td>' +
                '<td align="center" class="small align-middle">' + opt.tiporeg + '</td>' +
                '<td align="center" class="small align-middle">' + opt.docafectado + '</td>' +
                '<td align="center" class="small text-right">' + opt.totalcompraconiva + '</td>' +
                '<td align="center" class="small text-right">' + opt.mtoexento + '</td>' +
                '<td align="center" class="small text-right">' + opt.totalcompra + '</td>' +
                '<td align="center" class="small align-middle">' + opt.alicuota_iva + '%</td>' +
                '<td align="center" class="small text-right">' + opt.monto_iva + '</td>' +
                '<td align="center" class="small text-right">' + opt.retencioniva + '</td>' +
                '<td align="center" class="small align-middle">' + opt.porctreten + '%</td>' +
                '<td align="center" class="small align-middle">' + opt.fecharetencion + '</td>' +
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
        '<td align="center" class="small text-right" '+bold+'>' + totales.tcci + '</td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales.mtoex + '</td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales.totcom + '</td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales.mtoiva + '</td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales.retiva + '</td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
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

            $('#tabla1').append(
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