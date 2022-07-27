
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listar_tabladinamica();
}

$(document).ready(function(){

});

function listar_tabladinamica(){

    let fechai   = $('#fechai').val();
    let fechaf   = $('#fechaf').val();
    let vendedor = $('#vendedor').val();
    let marca    = $('#marca').val();
    let tipo     = $('#tipo').val();

    $.ajax({
        url: "tabladinamica_controlador.php?op=listar_tabladinamica",
        method: "POST",
        data: {fechai: fechai, fechaf: fechaf, edv: vendedor, marca: marca, tipo: tipo},
        dataType: "json", // Formato de datos que se espera en la respuesta
        beforeSend: function () {
            SweetAlertLoadingShow("Procesando información, espere...");
        },
        error: function (e) {
            SweetAlertError(e.responseText.substring(0, 400) + "...", "Error!");
            console.log(e.responseText);
        },
        success: function (datos) {

            tabladinamica(datos);
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

function tabladinamica(data) {
    let { tabla, totales } = data;

    if (!jQuery.isEmptyObject(tabla))
    {
        $.each(tabla, function(idx, opt) {
            $('#tabla').append(
                '<tr>' +
                '<td align="center" class="small align-middle">' + opt.num + '</td>' +
                '<td align="center" class="small align-middle">' + opt.codvend + '</td>' +
                '<td align="center" class="small align-middle">' + opt.vendedor + '</td>' +
                '<td align="center" class="small align-middle">' + opt.clasevend + '</td>' +
                '<td align="center" class="small align-middle">' + opt.tipo + '</td>' +
                '<td align="center" class="small align-middle">' + opt.numerod + '</td>' +
                '<td align="center" class="small align-middle">' + opt.codclie + '</td>' +
                '<td align="center" class="small align-middle">' + opt.cliente + '</td>' +
                '<td align="center" class="small align-middle">' + opt.codnestle + '</td>' +
                '<td align="center" class="small align-middle">' + opt.clasificacion + '</td>' +
                '<td align="center" class="small align-middle">' + opt.coditem + '</td>' +
                '<td align="center" class="small align-middle">' + opt.descripcion + '</td>' +
                '<td align="center" class="small align-middle">' + opt.marca + '</td>' +
                '<td align="center" class="small align-middle">' + opt.cantidad + '</td>' +
                '<td align="center" class="small align-middle">' + opt.unid + '</td>' +
                '<td align="center" class="small align-middle">' + opt.paq + '</td>' +
                '<td align="center" class="small align-middle">' + opt.bul + '</td>' +
                '<td align="center" class="small align-middle">' + opt.kg + '</td>' +
                '<td align="center" class="small align-middle">' + opt.instancia + '</td>' +
                '<td align="center" class="small align-middle">' + opt.montod + '</td>' +
                '<td align="center" class="small align-middle">' + opt.descuento + '</td>' +
                '<td align="center" class="small align-middle">' + opt.factor + '</td>' +
                '<td align="center" class="small align-middle">' + opt.montobs + '</td>' +
                '<td align="center" class="small align-middle">' + opt.fechae + '</td>' +
                '<td align="center" class="small align-middle">' + opt.mes + '</td>' +
                '</tr>'
            );
        });
    }

    bold  = 'style="font-weight: bold"';
    $('#tabla').append('<tr><td colspan="25">'+ "" +'</td></tr>');
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
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'>Totales</td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales.paqt + '</td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales.bult + '</td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales.kilo + '</td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'>' + totales.total + '</td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
        '<td align="center" class="small text-right" '+bold+'></td>' +
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
            $('#tabla1').append(
                '<tr>' +
                '<td align="center" class="small align-middle">' + opt.codvend + '</td>' +
                '<td align="center" class="small align-middle">' + opt.codclie + '</td>' +
                '<td align="center" class="small align-middle">' + opt.descrip + '</td>' +
                '<td align="center" class="small text-right">' + opt.descuentototal + '</td>' +
                '<td align="center" class="small text-right">' + opt.tasa + '</td>' +
                '<td align="center" class="small text-right">' + opt.descuentototalbs + '</td>' +
                '<td align="center" class="small align-middle">' + opt.numerod + '</td>' +
                '<td align="center" class="small align-middle">' + opt.tipofac + '</td>' +
                '<td align="center" class="small align-middle">' + opt.fechae + '</td>' +
                '</tr>'
            );
        });
    } else {
        //en caso de consulta vacia, mostramos un mensaje de vacio
        $('#tabla1').append('<tr><td colspan="7" align="center" class="small align-middle">Sin registros para esta Consulta</td></tr>');
    }
}

init();