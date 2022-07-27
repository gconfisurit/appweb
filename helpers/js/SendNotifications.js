
function send_notification_error(message = '') {
    let response = '';
    $.ajax({
        url: `${url}gestionsistema/gestionsistema_controlador.php?op=enviar_correo_error`,
        method: "POST",
        dataType: "json",
        data: {message: message},
        success: function (data) {
            response = data.status;
        }
    });
    return response;
}