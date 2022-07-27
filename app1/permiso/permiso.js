
//Función que se ejecuta al inicio
function init() {
    titulo_permisos();
    listar_permisos();

    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#rol_form").on("submit", function (e) {
        guardaryeditar(e);
    })

    //cambia el titulo de la ventana modal cuando se da click al boton
    /*$("#btnGestion").click(function () {
        window.location = ("../gestionsistema/gestionsistema.php");
    });*/

    $("#btnVolver").click(function () {
        switch (parseInt($("#tipo").val())) {
            case 0: //volver a roles
                window.location = ("../roles/roles.php");
                break;
            case 1: //volver a usuarios
                window.location = ("../usuario/usuario.php");
                break;
        }
    });


}

$(document).ready(function () {
    switch (parseInt($("#tipo").val())) {
        case 0: $('#btnVolver').val('Volver a roles'); break;
        case 1: $('#btnVolver').val('Volver a usuarios'); break;
    }
});

function titulo_permisos() {
    let isError = false;
    let id = $("#tipoid").val();
    let tipo = $("#tipo").val();
    $.ajax({
        url: 'permiso_controlador.php?op=obtener_descripcion',
        method: "POST",
        dataType: "json",
        data: {id: id, tipo: tipo},
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if (!jQuery.isEmptyObject(data)) {
                $('#title_permisos').text('Permisos de ' + data.descripcion.toUpperCase());
            }
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function listar_permisos() {
    let isError = false;
    let id = $("#tipoid").val();
    let tipo = $("#tipo").val();
    $.ajax({
        url: 'permiso_controlador.php?op=listar_permisos',
        method: "POST",
        dataType: "json",
        data: {id: id, tipo: tipo, esMenuLateral: 0},
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if (!jQuery.isEmptyObject(data)) {
                $('#permisos').html(permisosRecursion(data, ''));
            }
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function guardar(modulo_id) {
    let id = $("#tipoid").val();
    let tipo = $("#tipo").val();
    let state = document.getElementById('modulo_'+modulo_id).checked === true;
    $.ajax({
        url: 'permiso_controlador.php?op=guardar_permisos',
        type: "POST",
        dataType: "json",
        data: {tipo: tipo, id: id, modulo_id: modulo_id, state: state},
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            let { icono, mensaje } = data;
            ToastSweetMenssage(icono, mensaje);
        }
    });
}

init();