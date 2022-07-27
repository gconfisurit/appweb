
//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#spinner").css('visibility', 'hidden');
    listar_marcas();

    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox({
        infoTextFiltered: '<span class="badge badge-warning">Filtrados</span> {0} de {1}',
        filterPlaceHolder: 'Filtro de Búsqueda',
        filterTextClear: 'show all',
        infoText: 'Total {0}',
        infoTextEmpty: 'Lista Vacía',
        moveSelectedLabel: 'Mover seleccionado',
        moveAllLabel: 'Mover todos',
        removeSelectedLabel: 'Remover seleccionado',
        removeAllLabel: 'Remover todos',
        selectorMinimalHeight: 240,
    })
}

function listar_marcas() {
    let isError = false;
    $.ajax({
        async: false,
        url: "kpimarca_controlador.php?op=listar_marcas",
        type: "post",
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
                $.each(data.lista_marcas, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('.duallistbox').append('<option name="" value="' + opt.marca +'" '+(opt.selec ? "selected" : "")+'>' + opt.marca + '</option>');
                });
            }
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_guardar", function () {

    let form = $('#frm_kpimarcas').serialize();

    $.ajax({
        async: true,
        url: "kpimarca_controlador.php?op=guardar_kpiMarcas",
        method: "POST",
        dataType: "json",
        data: form,
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data)) {
                let { icono, mensaje } = data
                ToastSweetMenssage(icono, mensaje);
            }

        }
    });
});

init();