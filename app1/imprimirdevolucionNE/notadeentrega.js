
var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
}

function limpiar() {
    $("#nrodocumento").val("");
}

var no_puede_estar_vacio = function()
{
    ($("#nrodocumento").val() !== "")
        ? estado_minimizado = true : estado_minimizado = false ;
};

$(document).ready(function(){
    $("#nrodocumento").change( () => no_puede_estar_vacio() );
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    const documento = addZeros($("#nrodocumento").val());

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (documento !== "000000") {
            sessionStorage.setItem("nrodocumento", documento);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            $.ajax({
                url: "notadeentrega_controlador.php?op=buscar_notadeentrega",
                type: "post",
                dataType: "json",
                data: { nrodocumento: documento },
                beforeSend: function () {
                    SweetAlertLoadingShow();
                    $('#notadeentrega_data tbody').empty();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText.substring(0, 400) + "...", "Error!")
                    // send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    if(!jQuery.isEmptyObject(data)) {
                        let { empresa, cabecera, detalle } = data;

                        // datos empresa
                        if(!jQuery.isEmptyObject(empresa)) {
                            $('#descrip_empresa').text(empresa.descrip);
                            $('#rif_empresa').text(empresa.rif);
                            $('#direccion_empresa').text(empresa.direc1);
                            $('#telefono_empresa').text(empresa.telef);
                        }

                        // cabecera de la nota de entrega
                        if(!jQuery.isEmptyObject(cabecera)) {

                            if (parseFloat(cabecera.descuentoitem) === 0) {
                                $('#tfoot_observacion').attr('colspan', 4);
                                $('#tfoot_sinderecho').attr('colspan', 6);
                                $('#header_subtotal').hide();
                                $('#header_descuento').hide();
                            } else {
                                $('#tfoot_observacion').attr('colspan', 6);
                                $('#tfoot_sinderecho').attr('colspan', 8);
                                $('#header_subtotal').show();
                                $('#header_descuento').show();
                            }

                            if (parseFloat(cabecera.descuentototal) === 0) {
                                $('#footer_subtotal').hide();
                                $('#footer_descuentototal').hide();
                            } else {
                                $('#footer_subtotal').show();
                                $('#footer_descuentototal').show();
                            }

                            $('#cabecera_codclie').text(cabecera.codclie);
                            $('#cabecera_rif').text(cabecera.rif);
                            $('#cabecera_codvend').text(cabecera.codvend);
                            $('#cabecera_rsocial').text(cabecera.rsocial);
                            $('#cabecera_representante').text(cabecera.representante);
                            $('#cabecera_telefono').text(cabecera.telefono);
                            $('#cabecera_fechae').text(cabecera.fechae);
                            $('#cabecera_direccion').text(cabecera.direccion);
                            $('#cabecera_direccion2').text(cabecera.direccion2);
                            $('#numeront').text(cabecera.numerod);

                            $('#tfoot_subtotal').text(cabecera.subtotal);
                            $('#tfoot_descuentototal').text(cabecera.descuento);
                            $('#tfoot_observacion_value').text(cabecera.notas1);
                            $('#tfoot_totalnota').text(cabecera.total);
                        }

                        if(!jQuery.isEmptyObject(detalle)) {
                            $.each(detalle, function(idx, opt) {
                                if (parseFloat(cabecera.descuentoitem) > 0) {
                                    $('#notadeentrega_data').append(
                                        '<tr>' +
                                        '<td align="center" class="align-middle">' + opt.coditem + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.descripcion + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.cantidad + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.unidad + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.precio + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.totalitem + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.descuento + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.total + '</td>' +
                                        '</tr>'
                                    );
                                } else {
                                    $('#notadeentrega_data').append(
                                        '<tr>' +
                                        '<td align="center" class="align-middle">' + opt.coditem + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.descripcion + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.cantidad + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.unidad + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.precio + '</td>' +
                                        '<td align="center" class="align-middle">' + opt.total + '</td>' +
                                        '</tr>'
                                    );
                                }

                            });
                        }
                    }
                },
                complete: function () {
                    if (!isError) SweetAlertLoadingClose();
                    $("#tabla").show('');//MOSTRAMOS LA TABLA.
                    limpiar();//LIMPIAMOS EL SELECTOR.
                }
            });
            estado_minimizado = true;
        }
    } else {
        SweetAlertError('Debe ingresar un Numero de Nota de Entrega');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    const nrodocumento = sessionStorage.getItem("nrodocumento");
    if (nrodocumento !== "") {
        window.location = "notadeentrega_excel.php?&nrodocumento="+nrodocumento;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    const nrodocumento = sessionStorage.getItem("nrodocumento");
    if (nrodocumento !== "") {
        window.open('notadeentrega_pdf.php?&nrodocumento='+nrodocumento, '_blank');
    }
});

init();
