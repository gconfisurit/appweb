let tabla_mercancia;
let estado_minimizado_tabla_modal;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla_mercancia_por_despachar").slideUp();
    estado_minimizado_tabla_modal = false;

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

function limpiar_campo_almacenes_modal() {
    $('#checkbox').prop('checked', false);
    $('[name="depo[]"]').val("").trigger("change");
    $('[name="depo[]"]').attr("disabled", false);
}

function limpiar_modal_mercancia() {
    limpiar_campo_almacenes_modal();
    $('#relacion_mercancia tbody').empty();
    $('[name="depo[]"]').empty().trigger("change");
    $("#tabla_mercancia_por_despachar").slideUp();
    $('#tfoot_tbulto').text('Cant. Bultos');
    $('#tfoot_tpaq').text('Cant. Paquetes');
    $('#tfoot_tbultoinv').text('Inv. Bultos');
    $('#tfoot_tpaqinv').text('Inv. Paquetes');
    $('#factsindes').text('0');
    listar_almacenes();
}

function listar_almacenes() {
    let isError = false;
    $.ajax({
        url: "despachos_controlador.php?op=listar_depositos",
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
            if(!isError) {
                SweetAlertLoadingClose();

                $(".depo").change(() => {
                    ($(".depo").val().length > 0)
                        ? estado_minimizado_tabla_modal = true
                        : estado_minimizado_tabla_modal = false;
                });
            }
        }
    });
}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btnBuscarmercanciaModal", function () {

    const depo = $('[name="depo[]"]').val();

    if (estado_minimizado_tabla_modal) {
        estado_minimizado_tabla_modal = false;
        if (depo.length > 0) {
            const datos = $('#frminventario').serialize();
            sesionStorageItems(datos);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            $.ajax({
                url: "despachos_controlador.php?op=listar_mercancia_por_despachar",
                type: "POST",
                data: datos,
                dataType: "json",
                beforeSend: function () {
                    $("#tabla_mercancia_por_despachar").slideUp();
                    $('#relacion_mercancia tbody').empty();
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
                            $('#relacion_mercancia')
                                .append(
                                    '<tr>' +
                                    '<td align="center" class="small align-middle">' + opt.codprod + '</td>' +
                                    '<td align="center" class="small align-middle">' + opt.descrip + '</td>' +
                                    '<td align="center" class="small align-middle">' + opt.cant_bul + '</td>' +
                                    '<td align="center" class="small align-middle">' + opt.cant_paq + '</td>' +
                                    '<td align="center" class="small align-middle">' + opt.tinvbult + '</td>' +
                                    '<td align="center" class="small align-middle">' + opt.tinvpaq + '</td>' +
                                    '</tr>'
                                );
                        });
                        $('#tfoot_tbulto').text(totales_tabla.tbulto);
                        $('#tfoot_tpaq').text(totales_tabla.tpaq);
                        $('#tfoot_tbultoinv').text(totales_tabla.tbultoinv);
                        $('#tfoot_tpaqinv').text(totales_tabla.tpaqinv);
                        $('#factsindes').text(totales_tabla.facturas_sin_despachar);
                    }else {
                        //en caso de consulta vacia, mostramos un mensaje de vacio
                        $('#relacion_mercancia').append('<tr><td colspan="8" align="center">Sin registros para esta Consulta</td></tr>');
                    }
                },
                complete: function () {
                    if(!isError) {
                        limpiar_campo_almacenes_modal();//LIMPIAMOS EL SELECTOR.
                        estado_minimizado_tabla_modal = true;
                        SweetAlertLoadingClose();
                        $("#tabla_mercancia_por_despachar").slideDown();//MOSTRAMOS LA TABLA.
                    }
                }
            });

        }
    } else {
        SweetAlertError('Debe seleccionar al menos un Almacén!');
        return (false);
    }
});

function sesionStorageItems(datos){
    sessionStorage.setItem("datos", datos);
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#modalExportarExcel", function(){
    const datos = sessionStorage.getItem("datos");
    window.location = 'mercancia_por_despachar_excel.php?&'+datos;
});

init();