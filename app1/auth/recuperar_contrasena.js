
$(document).ready(function () {

});

function limpiarRecover() {
    $('#btnSend').show();
    $('#btnValidar').hide();
    $('#btnGuardar').hide();
    $('#loginModal').modal('hide')
    $("#errorRecover").hide();
    $("#mensajeRecover").html('');
    $('#email').prop('disabled', '');
    $('#email').val('');
    $('#div_codigo').html('');
    $('#div_clave1').html('');
    $('#div_clave2').html('');
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

function clickBtnSend() {
    if ($('#email').val().length > 3) {

        $.ajax({
            url: "auth/auth_controlador.php?op=generate_code_recovery_user",
            type: "POST",
            dataType: "json",
            data: {user: $('#email').val()},
            beforeSend: function () {
                $('#email').prop('disabled', 'disabled');
            },
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            success: function (callback) {
                let {  status, message } = callback

                if (status) {
                    $("#errorRecover").hide();
                    $('#div_codigo').html(input_codigo);
                    $('#mensajeRecover').html('');
                    $('#btnSend').hide();
                    $('#btnValidar').show();
                } else {
                    $("#errorRecover").show();
                    $('#btnSend').show();
                    $('#mensajeRecover').html(message);
                    $('#email').prop('disabled', '');
                }
            }
        });
    } else {
        $('#email').prop('disabled', '');
        $('#div_codigo').html('');
        $("#errorRecover").show();
        $('#mensajeRecover').html('Ingrese nombre de usuario !');
    }
}

function clickBtnValidar() {
    $('#email').prop('disabled', '');
    let user = $('#email').val();
    let code = $('#codigo').val();
    $('#email').prop('disabled', 'disabled');

    if (code.length > 3) {

        $.ajax({
            url: "auth/auth_controlador.php?op=validate_code_recovery_user",
            type: "POST",
            dataType: "json",
            data: {user: user, code: code},
            beforeSend: function () {
                $('#codigo').prop('disabled', 'disabled');
            },
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            success: function (callback) {
                let {  status, message } = callback

                if (status) {
                    $("#errorRecover").hide();
                    $('#div_clave1').html(input_clave1);
                    $('#div_clave2').html(input_clave2);
                    $('#mensajeRecover').html('');
                    $('#btnValidar').hide();
                    $('#btnGuardar').show();
                    $('[data-mask]').inputmask();
                } else {
                    $("#errorRecover").show();
                    $('#btnValidar').show();
                    $('#mensajeRecover').html(message);
                    $('#codigo').prop('disabled', '');
                }
            }
        });
    } else {
        $('#codigo').prop('disabled', '');
        $('#div_clave1').html('');
        $('#div_clave2').html('');
        $("#errorRecover").show();
        $('#mensajeRecover').html('Ingrese codigo de seguridad !');
    }
}

function clickBtnGuardar() {
    let flag = false;
    $('#email').prop('disabled', '');
    let user = $('#email').val();
    let clave1 = $('#clave1').val();
    let clave2 = $('#clave2').val();
    $('#email').prop('disabled', 'disabled');

    if (clave1.length > 3 && clave2.length > 3) {

        $.ajax({
            url: "auth/auth_controlador.php?op=change_password_user",
            type: "POST",
            dataType: "json",
            data: {
                user: user,
                clave1: clave1,
                clave2: clave2
            },
            beforeSend: function () {
                $('#clave1').prop('disabled', 'disabled');
                $('#clave2').prop('disabled', 'disabled');
            },
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            success: function (callback) {
                let {  status, message } = callback
                flag = (status);
            }
        });

        if (flag) {
            $.ajax({
                url: "auth/auth_controlador.php?op=login_in",
                type: "POST",
                dataType: "json",
                data: {login: user, clave: clave1},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    console.log(e.responseText);
                },
                success: function (callback) {
                    let {  status, message, data } = callback

                    if (status) {
                        window.location = 'principal.php'
                    }
                }
            });
        } else {
            SweetAlertError('Error.');
        }
    } else {
        $('#codigo').prop('disabled', '');
        $('#div_clave1').html('');
        $('#div_clave2').html('');
        $("#errorRecover").show();
        $('#mensajeRecover').html('Ingrese codigo de seguridad !');
    }
}

let input_codigo = () => {
    return `<div class="input-group-prepend">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
            <input id="codigo" name="codigo" type="text" class="form-control" 
            placeholder="Codigo de seguridad enviado al correo" 
            data-inputmask='"mask": "9999"' data-mask> `;
}

let input_clave1 = () => {
    return `<div class="input-group-prepend">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
            <input id="clave1" name="clave1" type="password" class="form-control" placeholder="Nueva Contraseña"> `;
}

let input_clave2 = () => {
    return `<div class="input-group-prepend">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
            <input id="clave2" name="clave2" type="password" class="form-control" placeholder="Repetir Nueva Contraseña"> `;
}