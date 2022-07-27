let validator;

$(document).ready(function () {

    $("#btnAcceder").click(function () {
        login();
    });

    $("#btnLogin").click(function () {
        limpiar();
        $('#loginModal').modal('show');
    });

    validaciones();
});

function limpiar() {
    $("#error").hide();
    $('#login').removeClass('is-invalid');
    $('#clave').removeClass('is-invalid');
}

function login() {
    let user = $('#login').val()
    let clave = $('#clave').val()
    // const formData = new FormData($("#login_form")[0]);
    $.ajax({
        url: "auth/auth_controlador.php?op=login_in",
        type: "POST",
        dataType: "json",
        data: {login:user, clave:clave},
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (callback) {
            let {  status, message, data } = callback

            if (status) {
                $("#error").hide();
                $('#login_form')[0].reset();
                $('#loginModal').modal('hide');
                limpiar();

                window.location = 'principal.php'
            } else {
                $("#error").show();
                $('#mensaje').html(message);
            }

        }
    });
}

function validaciones() {
    validator = $('#login_form').validate({
        rules: {
            login: {
                required: true,
                minlength: 3
            },
            clave: {
                required: true,
                minlength: 6
            },
        },
        messages: {
            login: {
                required: "Campo requerido",
                minlength: "El Campo debe contener al menos 3 caracteres"
            },
            clave: {
                required: "Campo requerido",
                minlength: "El Campo debe contener al menos 6 caracteres"
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
}