
let tabla_parametros;

function init() {
    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#modulo_form").on("submit", function (e) {
        guardarModulo(e);
    });

    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#nuevoparametro_form").on("submit", function (e) {
        e.preventDefault(); //No se activará la acción predeterminada del evento
        const modulo = $('#nuevoparametro_form #name_modulo').val();
        const parametro = $('#nuevoparametro_form #nuevo_parametro').val();
        const valor = $('#nuevoparametro_form #valor').val();
        guardarParametro(modulo, parametro, 0, valor);
    });

    $("#custom-tabs-one-tab li a").on("click", function (e) {

        switch (this.id.substr(11, 35)) {
            case 'parametros':
                listar_parametros();
                break;
            case 'notificaciones':
                break;
        }
    });

    $("#custom-tab-parametros").trigger("click");
}

/*funcion para limpiar formulario de modal*/
function limpiar_modulo() {
    $('#name_modulo').val('');
    $('#modulo_form')[0].reset();
    $('#modulo').html('<option value="">--Seleccione--</option>');
}

/*funcion para limpiar formulario de modal*/
function limpiar_parametro() {
    $('#details_parameters_name').val('');
    $('#nuevoparametro_form')[0].reset();
}

$(document).ready(function () {
    //
});

function mostrar_nuevo_parametro() {
    let isError = false;
    limpiar_parametro();

    let name_modulo = $('#detalleparametroModal #name_modulo').val();

    $('#nuevoparametroModal').modal('show');
    $("#modulo_name_title").text(name_modulo);
    $("#nuevoparametroModal #name_modulo").val(name_modulo);
}

//function listar
function listar_parametros() {
    let isError = false;
    //TABLA
    tabla_parametros = $('#parametro_data').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'gestionsistema_controlador.php?op=listar_modulos_json',
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

function mostrar_parametros(name_modulo= "") {
    let isError = false;

    $('#detalleparametroModal').modal('show');
    $("#details_module_name").text(name_modulo);
    $("#detalleparametroModal #name_modulo").val(name_modulo);

    $('#relacion_detalle_parametros').dataTable({
        "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
        "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
        "ajax": {
            url: "gestionsistema_controlador.php?op=mostrar_parametros_modal",
            type: "post",
            dataType: "json",
            data: {name_modulo: name_modulo},
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
        },//TRADUCCION DEL DATATABLE.
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]],
        "language": texto_español_datatables
    });
}

function guardarParametro(name_modulo= "", parameter = "", number = 0, valor = "") {

    let value = (number !== 0)
        ? $('#parametro_'+number).val()
        : valor;

    if (name_modulo !== "" && parameter !== "" && value !== "") {
        $.ajax({
            url: "gestionsistema_controlador.php?op=guardar_parametro_modal",
            type: "POST",
            data: {
                name_modulo: name_modulo,
                parameter: parameter,
                value: value,
            },
            dataType: "json",
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                let { icono, mensaje } = data
                ToastSweetMenssage(icono, mensaje);

                //verifica si el mensaje de insercion contiene error
                if(mensaje.includes('error')) {
                    return (false);
                } else {
                    limpiar_parametro();
                    $('#nuevoparametroModal').modal('hide');
                    if (valor !== "") {
                        $('#relacion_detalle_parametros').DataTable().ajax.reload();
                        $('#parametro_data').DataTable().ajax.reload();
                    }
                }
            }
        });
    }
}

function eliminar_parametro(name_modulo= "", parameter = "") {

    if (name_modulo !== "" && parameter !== "") {
        $.ajax({
            url: "gestionsistema_controlador.php?op=eliminar_parametro_modal",
            type: "POST",
            data: {
                name_modulo: name_modulo,
                parameter: parameter
            },
            dataType: "json",
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                let { icono, mensaje } = data
                ToastSweetMenssage(icono, mensaje);

                //verifica si el mensaje de insercion contiene error
                if(mensaje.includes('error')) {
                    return (false);
                } else {
                    $('#relacion_detalle_parametros').DataTable().ajax.reload();
                    $('#parametro_data').DataTable().ajax.reload();
                }
            }
        });
    }
}

function mostrar_modulo(name_modulo= -1) {
    let isError = false;
    limpiar_modulo();

    $('#moduloModal').modal('show');

    $.ajax({
        url: "gestionsistema_controlador.php?op=mostrar_modulo",
        method: "POST",
        dataType: "json",
        data: {name_modulo: name_modulo},
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            //lista de seleccion
            $.each(data.lista_modulos, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#modulo').append('<option name="" value="' + opt +'">' + opt.substr(0, 35) + '</option>');
            });

            if(name_modulo !== -1) {
                $('.modal-title').text("Editar Módulo");
                $('#name_modulo').val(name_modulo);
            }

        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

//la funcion guardaryeditar(e); se llama cuando se da click al boton submit
function guardarModulo(e) {

    e.preventDefault(); //No se activará la acción predeterminada del evento
    let modulo = $('#modulo_form #modulo').val();
    const formData = new FormData($("#modulo_form")[0]);

    if (modulo !== "") {
        $.ajax({
            url: "gestionsistema_controlador.php?op=guardar_modulo",
            type: "POST",
            data: {name_modulo: modulo},
            dataType: "json",
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                let { icono, mensaje } = data
                ToastSweetMenssage(icono, mensaje);

                //verifica si el mensaje de insercion contiene error
                if(mensaje.includes('error')) {
                    return (false);
                } else {
                    $('#modulo_form')[0].reset();
                    $('#moduloModal').modal('hide');
                    $('#parametro_data').DataTable().ajax.reload();
                    limpiar_modulo();
                }
            }
        });
    } else {
        SweetAlertError('Debe seleccionar un MÓDULO para continuar.');
        return (false);
    }
}

function eliminar_modulo(name_modulo) {

    Swal.fire({
        // title: '¿Estas Seguro?',
        text: "¿Estas Seguro de Eliminar el módulo ("+name_modulo+") ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "gestionsistema_controlador.php?op=eliminar_modulo",
                method: "POST",
                dataType: "json",
                data: {name_modulo: name_modulo},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    ToastSweetMenssage(data.icono, data.mensaje);
                    $('#parametro_data').DataTable().ajax.reload();
                }
            });
        }
    })
}

//Mostrar datos del usuario en la ventana modal del formularioS
init();






