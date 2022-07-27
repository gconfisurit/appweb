var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_vendedores();
}

function limpiar() {
    $("#opc").val("");
    $("#vendedor").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    ($("#opc").val() !== "" && $("#vendedor").val() !== "") ? estado_minimizado = true : estado_minimizado = false;
};

function listar_vendedores() {
    let isError = false;
    $.ajax({
        url: "clientescodnestle_controlador.php?op=listar_vendedores",
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
            if(!jQuery.isEmptyObject(data.lista_vendedores)){
                //lista de seleccion de vendedores
                $('#vendedor').append('<option name="" value="">Seleccione</option>');
                $.each(data.lista_vendedores, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#vendedor').append('<option name="" value="' + opt.CodVend +'">' + opt.CodVend + ': ' + opt.Descrip.substr(0, 35) + '</option>');
                });
            }
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

$(document).ready(function () {
    $("#opc").change(() => no_puede_estar_vacio());
    $("#vendedor").change(() => no_puede_estar_vacio());
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_clientescodnestle", function () {

    let opc = 0;

    if (document.getElementById('todos').checked === false &&
        document.getElementById('concode').checked === false &&
        document.getElementById('sincode').checked === false
    ) {
        Swal.fire('Atención!', 'Debe Seleccionar una Opción!', 'error');
        return (false);
    } else {
        if (document.getElementById('todos').checked === true) {
            opc = 1;
        }
        if (document.getElementById('concode').checked === true) {
            opc = 2;
        }
        if (document.getElementById('sincode').checked === true) {
            opc = 3;
        }
    }
    var vendedor = $("#vendedor").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (opc !== "0" && vendedor !== "") {
            let isError = false;
            sessionStorage.setItem("opc", opc);
            sessionStorage.setItem("vendedor", vendedor);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#clientescodnestle_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "clientescodnestle_controlador.php?op=buscar_clientescodnestle",
                    type: "post",
                    dataType: "json",
                    data: {opc: opc, vendedor: vendedor},
                    beforeSend: function () {
                        SweetAlertLoadingShow();
                    },
                    error: function (e) {
                        isError = SweetAlertError(e.responseText, "Error!")
                        send_notification_error(e.responseText);
                        console.log(e.responseText);
                    },
                    complete: function () {
                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        if(!isError) SweetAlertLoadingClose();
                        validarCantidadRegistrosTabla();
                        limpiar();//LIMPIAMOS EL SELECTOR.
                    }
                },//TRADUCCION DEL DATATABLE.
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 10,
                "order": [[0, "desc"]],
                "language": texto_español_datatables
            });
            estado_minimizado = true;
        }
    } else {
        SweetAlertError('Debe seleccionar un vendedor.')
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click", "#btn_excel", function () {
    var opc = sessionStorage.getItem("opc", opc);
    var vendedor = sessionStorage.getItem("vendedor");
    if (opc !== "" && vendedor !== "") {
        window.location = "clientescodnestle_excel.php?&opc=" + opc + "&vendedor=" + vendedor;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click", "#btn_pdf", function () {
    var opc = sessionStorage.getItem("opc", opc);
    var vendedor = sessionStorage.getItem("vendedor");
    if (opc !== "" && vendedor !== "") {
        window.open('clientescodnestle_pdf.php?&opc=' + opc + '&vendedor=' + vendedor, '_blank');
    }
});

init();
