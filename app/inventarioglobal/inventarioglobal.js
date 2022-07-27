var tabla;
var estado_minimizado;
var depo;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;

    listar_almacenes();

    $("#checkbox").on("click", function (e) {
        if($("#checkbox").is(':checked') ) {
            $(".depo > option").prop("selected","selected");
        } else {
            $(".depo > option").prop("selected","");
        }
        $(".depo").trigger("change");
    });
}

function limpiar() {
    $('#checkbox').prop('checked', false);
    $('[name="depo[]"]').val("").trigger("change");
    $('[name="depo[]"]').attr("disabled", false);
    $('#btn_excel').attr("disabled", false);
    $('#btn_pdf').attr("disabled", false);
    $('#tfoot_cantbul_x_des').text("");
    $('#tfoot_cantpaq_x_des').text("");
    $('#tfoot_cantbul_sistema').text("");
    $('#tfoot_cantpaq_sistema').text("");
    $('#tfoot_totalbul_inv').text("");
    $('#tfoot_totalpaq_inv').text("");
    $('#cuenta').text("");
}

var no_puede_estar_vacio = function () {
    ($(".depo").val().length > 0) ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function () {
    $(".depo").change(() => no_puede_estar_vacio());
});

function listar_almacenes() {
    let isError = false;
    $.ajax({
        url: "inventarioglobal_controlador.php?op=listar_depositos",
        type: "GET",
        dataType: "json",
        beforeSend: function () {
            //mientras carga inabilitamos el select
            $('[name="depo[]"]').attr("disabled", true);
            SweetAlertLoadingShow();
        },
        error: function(e){
            isError = SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function(data) {
            //cuando termina la consulta, activamos el boton
            $('[name="depo[]"]').attr("disabled", false);
            //lista de seleccion de depositos
            $.each(data.lista_depositos, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('[name="depo[]"]').append('<option name="" value="' + opt.codubi +'">' + opt.codubi + ': '+ opt.descrip.substr(0, 35) + '</option>');
            });
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}


//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_inventarioglobal", function () {

    let depo = $('[name="depo[]"]').val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (depo.length > 0) {
            let datos = $('#frminventario').serialize();
            //almacenamos en sesion una variable
            sessionStorage.setItem("datos", datos);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            $.ajax({
                url: "inventarioglobal_controlador.php?op=listar_inventarioglobal",
                type: "POST",
                data: datos,
                dataType: "json",
                beforeSend: function () {
                    limpiar();//LIMPIAMOS EL SELECTOR.
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText, "Error!")
                    console.log(e.responseText);
                },
                success: function(data) {
                    if(!jQuery.isEmptyObject(data)) {
                        let {contenido_tabla, totales_tabla} = data;
                        //TABLA
                        $.each(contenido_tabla, function (idx, opt) {
                            $('#inventarioglobal_data')
                                .append(
                                    '<tr>' +
                                        '<td align="center" class="align-middle">' + opt.codprod + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.descrip + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.cant_bul + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.cant_paq + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.invbut + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.invpaq + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.tinvbult + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.tinvpaq + '</td>' +
                                    '</tr>'
                                );
                        });
                        $('#tfoot_cantbul_x_des').text(totales_tabla.tbulto);
                        $('#tfoot_cantpaq_x_des').text(totales_tabla.tpaq);
                        $('#tfoot_cantbul_sistema').text(totales_tabla.tbultsaint);
                        $('#tfoot_cantpaq_sistema').text(totales_tabla.tpaqsaint);
                        $('#tfoot_totalbul_inv').text(totales_tabla.tbultoinv);
                        $('#tfoot_totalpaq_inv').text(totales_tabla.tpaqinv);
                        $('#cuenta').text("Total Facturas sin Despachar: " + totales_tabla.facturas_sin_despachar);
                    } else {
                        //en caso de consulta vacia, mostramos un mensaje de vacio
                        $('#inventarioglobal_data').append('<tr><td colspan="8" align="center">Sin registros para esta Consulta</td></tr>');
                    }
                },
                complete: function () {
                    $("#tabla").show('');//MOSTRAMOS LA TABLA.
                    if(!isError) {
                        estado_minimizado = true;
                        SweetAlertLoadingClose();
                    }
                }
            });

        }
    } else {
        SweetAlertError('Debe seleccionar al menos un Almacén!');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var datos = sessionStorage.getItem("datos");
    if (datos !== "") {
        window.location = 'inventarioglobal_excel.php?&'+datos;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var datos = sessionStorage.getItem("datos");
    if (datos !== "") {
        window.open('inventarioglobal_pdf.php?&'+datos, '_blank');
    }
});

init();
