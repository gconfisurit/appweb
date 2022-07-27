var tabla;

//Función que se ejecuta al inicio
function init() {
    listar();

    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#usuario_form").on("submit", function (e) {
        guardaryeditar(e);
    });

    //cambia el titulo de la ventana modal cuando se da click al boton
    $("#add_button").click(function () {

        //habilita los campos cuando se agrega un registro nuevo ya que cuando se editaba un registro asociado entonces aparecia deshabilitado los campos
        $("#cedula").attr('disabled', false);
        $("#login").attr('disabled', false);
        $("#nomper").attr('disabled', false);

        $(".modal-title").text("Agregar Usuario");

    });
}

/*funcion para limpiar formulario de modal*/
function limpiar() {
    $("#cedula").val("");
    $('#login').val("");
    $('#nomper').val("");
    $('#email').val("");
    $('#clave').val("");
    $('#rol').html("");
    $('#estado').val("");
    $('#id_usuario').val("");
}

/*funcion para limpiar formulario de modal*/
function limpiar_modal_datos_cliente() {
    $('#tipoid3').val("").change();
    $('#cliente_form')[0].reset();
    $("#tipoid3").prop("disabled", false);
    $("#codclie").prop("readonly", false);
    $("#title_clienteModal").text("Agregar Cliente");
}

//function listar
function listar() {
    let isError = false;
    tabla = $('#usuario_data').dataTable({

        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'usuario_controlador.php?op=listar',
                type: "get",
                dataType: "json",
                beforeSend: function () {
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                complete: function () {
                    if(!isError) SweetAlertLoadingClose();
                }
            },

        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,//Por cada 10 registros hace una paginación
        "order": [[0, "asc"]],//Ordenar (columna,orden)
        "language": texto_español_datatables
    }).DataTable();
}

function cambiarEstado(id, est) {

    Swal.fire({
        title: '¿Estas Seguro?',
        text: "¿De realizar el cambio de estado?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, cambiar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "usuario_controlador.php?op=activarydesactivar",
                method: "POST",
                data: {id: id, est: est},
                success: function (data) {
                    $('#usuario_data').DataTable().ajax.reload();
                }
            });
        }
    })
}

function mostrar(id_usuario= -1) {
    let isError = false;
    limpiar();
    $('#usuarioModal').modal('show');
    //si es -1 el modal es crear usuario nuevo
    if(id_usuario === -1)
    {
        $.ajax({
            url: "usuario_controlador.php?op=mostrar",
            method: "POST",
            dataType: "json",
            data: {id_usuario: id_usuario},
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                //lista de seleccion de roles
                $('#rol').append('<option name="" value="">Seleccione un rol de usuario</option>');
                $.each(data.lista_roles, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#rol').append('<option name="" value="' + opt.id +'">' + opt.descripcion.substr(0, 35) + '</option>');
                });

            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });
    }// si no es -1, el modal muestra los datos de un usuario por su id
    else if(id_usuario !== -1) {
        $.ajax({
            url: "usuario_controlador.php?op=mostrar",
            method: "POST",
            dataType: "json",
            data: {id_usuario: id_usuario},
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                //lista de seleccion de roles
                $('#rol').append('<option name="" value="">Seleccione un rol de usuario</option>');
                $.each(data.lista_roles, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#rol').append('<option name="" value="' + opt.id +'">' + opt.descripcion.substr(0, 35) + '</option>');
                });

                $('#cedula').val(data.cedula);
                $("#cedula").prop("disabled", true);
                $('#login').val(data.login);
                $("#login").prop("disabled", false);
                $('#nomper').val(data.nomper);
                $("#nomper").prop("disabled", false);
                $('#email').val(data.email);
                $('#clave').val(data.clave);
                $('#rol').val(data.rol);
                $('#estado').val(data.estado);
                $('.modal-title').text("Editar Usuario");
                $('#id_usuario').val(id_usuario);

            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });
    }
}

//la funcion guardaryeditar(e); se llama cuando se da click al boton submit

function guardaryeditar(e) {

    e.preventDefault(); //No se activará la acción predeterminada del evento
    $("#cedula").prop("disabled", false);
    const formData = new FormData($("#usuario_form")[0]);
    $("#cedula").prop("disabled", true);

    $.ajax({
        url: "usuario_controlador.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            let { icono, mensaje } = data
            ToastSweetMenssage(icono, mensaje);

            $('#usuario_form')[0].reset();
            $('#usuarioModal').modal('hide');
            $('#usuario_data').DataTable().ajax.reload();
            limpiar();
        }
    });
}

function eliminar(cedula, usuario) {

    Swal.fire({
        // title: '¿Estas Seguro?',
        text: "¿Estas Seguro de Eliminar el usuario "+usuario+" ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "usuario_controlador.php?op=eliminar",
                method: "POST",
                dataType: "json",
                data: {cedula: cedula},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    ToastSweetMenssage(data.icono, data.mensaje);
                    $('#usuario_data').DataTable().ajax.reload();
                }
            });
        }
    })
}

//Mostrar datos del usuario en la ventana modal del formularioS
init();






