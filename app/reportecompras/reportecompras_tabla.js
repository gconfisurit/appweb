
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listar_reportecompras();

    $("#btn_excel").on("click", function (e) {
        const fechai = $("#fechaf").val();
        const fechaf = $("#fechaf").val();
        const marca = $("#marca").val();
        const datos=$('#form_reportecompras').serialize();

        window.location = "reportecompras_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&marca="+marca+"&"+datos;
    });

    $("#btn_pdf").on("click", function (e) {
        const fechai = $("#fechaf").val();
        const fechaf = $("#fechaf").val();
        const marca = $("#marca").val();
        const datos=$('#form_reportecompras').serialize();

        window.open('reportecompras_pdf.php?&fechai='+fechai+'&fechaf='+fechaf+'&marca='+marca+'&'+datos, '_blank');
    });
}

$(document).ready(function(){

});

function listar_reportecompras(){

    let fechai    = $('#fechaf').val();
    let fechaf    = $('#fechaf').val();
    let marca    = $('#marca').val();

    $.ajax({
        url: "reportecompras_controlador.php?op=listar",
        method: "POST",
        data: {fechai: fechai, fechaf: fechaf, marca: marca},
        dataType: "json", // Formato de datos que se espera en la respuesta
        beforeSend: function () {
            SweetAlertLoadingShow("Procesando información, espere...");
        },
        error: function (e) {
            SweetAlertError(e.responseText.substring(0, 400) + "...", "Error!")
            console.log(e.responseText);
        },
        success: function (datos) {

            tablareportecompras(datos);
        },
        complete: function () {
            SweetAlertSuccessLoading();
            const arr = ['#tabla'];

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

function tablareportecompras(data) {
    let { contenido_tabla } = data;

    if (!jQuery.isEmptyObject(contenido_tabla))
    {
        $.each(contenido_tabla, function(idx, opt) {
            var bg_alert = (parseInt(opt.rentabilidad) >= 30) ? "#ff3939" : ""

            $('#tabla')
                .append(
                    '<tr>' +
                    '<td  align="center" class="small align-middle">' + opt.num + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.codproducto + '</td>' +
                    '<td  align="center" class="small text-left">' + opt.descrip + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.displaybultos + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.costodisplay + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.costobultos + '</td>' +
                    '<td  align="center" class="small align-middle" BGCOLOR="'+bg_alert+'" >' + opt.rentabilidad + '%</td>' +
                    '<td  align="center" class="small align-middle">' + opt.fechapenultimacompra + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.bultospenultimacompra + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.fechaultimacompra + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.bultosultimacompra + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.semana1 + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.semana2 + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.semana3 + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.semana4 + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.totalventasmesanterior + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.bultosexistentes + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.productonovendidos + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.diasdeinventario + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.sugerido + '</td>' +
                    '<td  align="center" class="small align-middle">' + opt.pedido + '</td>' +
                    '</tr>'
                );
        });
    }
}

init();