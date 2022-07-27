let estado_vacio;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    estado_vacio = true;
    listar_marcas();
}

function limpiar() {
   // $("#fechai").val("");
    $("#fechaf").val("");
    $("#marca").val("");
}

function listar_marcas() {
    let isError = false;
    $.ajax({
        url: "reportecompras_controlador.php?op=listar_marcas",
        type: "GET",
        dataType: "json",
        beforeSend: function () {
            SweetAlertLoadingShow();
            //mientras carga inabilitamos el select
            $("#marca").attr("disabled", true);
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function(data) {
            //cuando termina la consulta, activamos el boton
            $("#marca").attr("disabled", false);
            //lista de seleccion de las marcas
            $('#marca')
                .append('<option name="" value="">Seleccione una Marca</option>')
                .append('<option name="" value="-">TODAS</option>');
            $.each(data.lista_marcas, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#marca').append('<option name="" value="' + opt.marca +'">' + opt.marca + '</option>');
            });
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

var no_puede_estar_vacio = function ()
{
    ( $("#fechaf").val() !== ""  &&  $("#marca").val() !== "")
        ? estado_vacio = false : estado_vacio = true;
};

$(document).ready(function () {
    $("#fechaf").change(() => no_puede_estar_vacio());
    $("#marca").change(() => no_puede_estar_vacio());
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    let fechai = $("#fechaf").val();
    let fechaf = $("#fechaf").val();
    let marca = $("#marca").val();
    if (!estado_vacio && fechai !== "" && fechaf !== "" && marca !== "") {
        window.open("reportecompras_tabla.php?&fechai="+fechai+"&fechaf="+fechaf+"&marca="+marca, '_blank');
        limpiar();
        estado_vacio = true;
    }else {

        if (fechai === "") {
            SweetAlertError('Debe seleccionar la fecha inicial!');
            return false;
        }
        if (fechaf === "") {
            SweetAlertError('Debe seleccionar la fecha final!');
            return false;
        }
        if (marca === "" ) {
            SweetAlertError('Debe seleccionar una Marca!');
            return false;
        }
    }
});

init();