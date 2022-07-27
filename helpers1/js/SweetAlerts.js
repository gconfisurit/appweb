
function SweetAlertLoadingShow(message = "Cargando, espere...") {
    swal.fire({
        html: '<h5>'+ message + '</h5>',
        showConfirmButton: false,
        allowOutsideClick: false,
        onRender: function() {
            // there will only ever be one sweet alert open.
            $('.swal2-content').prepend(sweet_loader);
        }
    });
}

function SweetAlertSuccessLoading(message = "Carga completa!") {
    swal.fire({
        icon: 'success',
        showConfirmButton: false,
        allowOutsideClick: false,
        timer: 1000,
        html: '<h5>'+ message +'</h5>'
    });
}

function SweetAlertLoadingClose() { swal.close() }

function SweetAlertError(message = "",title = "Atención!", icon = "error") {
    Swal.fire({
        title: title,
        html: message.substring(0, 400) + "...",
        icon: icon,
        allowOutsideClick: false
    });
    return true;
}

function ToastSweetMenssage(icon = 'success', message = 'Proceso Exitoso!') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    })
    Toast.fire({
        icon: icon,
        title: message
    })
}