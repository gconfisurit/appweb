let estado_vacio;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    estado_vacio = true;
    listar_vendedores();
    listar_marcas();
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#marca").val("");
}

var no_puede_estar_vacio = function()
{
    ($("#fechai").val() !== "" && $("#fechaf").val() !== ""
        && $("#marca").val() !== "" && $("#vendedor").val() !== "")
        ? estado_vacio = false : estado_vacio = true;
};

$(document).ready(function(){
    $("#fechai").change(() => no_puede_estar_vacio());
    $("#fechaf").change(() => no_puede_estar_vacio());
    $("#vendedor").change( () => no_puede_estar_vacio() );
    $("#marca").change(() => no_puede_estar_vacio());
});

function listar_marcas() {
    let isError = false;
    $.ajax({
        url: "sabana_tabladinamica_controlador.php?op=listar_marcas",
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
            if(!jQuery.isEmptyObject(data.lista_marcas)){
                //lista de seleccion de vendedores
                $('#marca')
                    .append('<option name="" value="">--Seleccione--</option>')
                    .append('<option name="" value="-">TODAS</option>');
                $.each(data.lista_marcas, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#marca').append('<option name="" value="' + opt.marca +'">' + opt.marca + '</option>');
                });
            }
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function listar_vendedores() {
    let isError = false;
    $.ajax({
        url: "sabana_tabladinamica_controlador.php?op=listar_vendedores",
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
                $('#vendedor').append('<option name="" value="">--Seleccione--</option>')
                    .append('<option name="" value="-">TODOS</option>');
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

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    let fechai = $("#fechai").val();
    let fechaf = $("#fechaf").val();
    let vendedor = $("#vendedor").val();
    let marca = $("#marca").val();
    let tipo = $('input:radio[name=tipo]:checked').val()

    if (!estado_vacio && fechai !== "" && fechaf !== "" && vendedor !== "" && marca !== "") {
        window.open("sabana_tabladinamica_tabla.php?&fechai="+fechai+"&fechaf="+fechaf+"&vendedor="+vendedor+"&marca="+marca+"&t="+tipo, '_blank');
        limpiar();
        estado_vacio = true;
    } else {

        if (fechai === "") {
            SweetAlertError('Debe Colocar la fecha inicial!');
            return false;
        }
        if (fechaf === "" ) {
            SweetAlertError('Debe Colocar la fecha final!');
            return false;
        }
        if (vendedor === "" ) {
            SweetAlertError('Debe seleccionar un vendedor!');
            return false;
        }
        if (marca === "" ) {
            SweetAlertError('Debe seleccionar una marca');
            return false;
        }
    }
});

init();
