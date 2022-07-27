
let tabla_modulo;
let tabla_menu;

let icon;

let oc;
let datasource = {
    'title': 'Menú lateral',
    'name': 'menu principal',
    'children': [
        {
            'title': 'Inicio',
            'name': 'vista home'
        }
    ],
};

function init() {
    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#modulo_form").on("submit", function (e) {
        guardaryeditarModulo(e);
    });

    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#menu_form").on("submit", function (e) {
        guardaryeditarMenu(e);
    });

    $("#custom-tabs-one-tab li a").on("click", function (e) {

        switch (this.id.substr(11, 35)) {
            case 'modulos':
                listar_modulos();
                break;
            case 'menu':
                cargar_datosGrafica();
                graficarDiagrama();
                listar_menus();
                break;
            case 'notificaciones':
                break;
        }
    });

    $("#custom-tab-modulos").trigger("click");
}

/*funcion para limpiar formulario de modal*/
function limpiar_modulo() {
    $('#id_modulo').val('');
    $('#modulo_form')[0].reset();
    $(".modal-title").text("Agregar Módulo");
    $('#ruta').html('<option value="">--Seleccione--</option>');
    $('#menu_id').html('<option value="">--Seleccione--</option>');
    $('#moduloModal #icono').val('').change();
    icon = '';
}

/*funcion para limpiar formulario de modal*/
function limpiar_menu() {
    $('#id_menu').val('');
    $('#menu_form')[0].reset();
    $(".modal-title").text("Agregar Menú");
    $('#menu_padre').html('');
    $('#menu_hijo').html('');
    $('#menuModal #icono').val('').change();
    icon = '';
}

$(document).ready(function () {
    $("#moduloModal #icono").change(() => {
        $('#moduloModal #icon').removeClass(icon).addClass($("#moduloModal #icono").val());
        icon = $("#moduloModal #icono").val();
    });

    $("#moduloModal #icono").on('keyup', () => {
        $('#moduloModal #icon').removeClass(icon).addClass($("#moduloModal #icono").val());
        icon = $("#moduloModal #icono").val();
    }).keyup();

    $("#menuModal #icono").change(() => {
        $('#menuModal #icon').removeClass(icon).addClass($("#menuModal #icono").val());
        icon = $("#menuModal #icono").val();
    });

    $("#menuModal #icono").on('keyup', () => {
        $('#menuModal #icon').removeClass(icon).addClass($("#menuModal #icono").val());
        icon = $("#menuModal #icono").val();
    }).keyup();
});

/*$(window).resize(function() {
    let width = $(window).width();
    if(width > 576) {
        oc.init({'verticalLevel': 3});
    } else {
        oc.init({'verticalLevel': 2});
    }
});*/

function cargar_datosGrafica() {
    $.ajax({
        async: false,
        cache: true,
        url: 'gestionit_controlador.php?op=datos_grafico_menus',
        method: "post",
        dataType: "json",
        success: function (data) {
            if (!jQuery.isEmptyObject(data)) {
                datasource = data;
            }
        }
    });
}

function graficarDiagrama() {
    oc = $('#chart-container').orgchart({
        'data' : datasource,
        'nodeTitle': 'title',
        'nodeContent': 'name',
        'verticalLevel': 3,
        'zoom': true,
        'zoominLimit': 1.3,
        'zoomoutLimit': 0.7
    });
}

//function listar
function listar_modulos() {
    let isError = false;
    tabla_modulo = $('#modulo_data').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'gestionit_controlador.php?op=listar_modulos',
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

//function listar
function listar_menus() {
    let isError = false;
    tabla_menu = $('#menu_data').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'gestionit_controlador.php?op=listar_menu',
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
        'columnDefs' : [{
            'visible': false, 'targets': [3]
        }],
        "language": texto_español_datatables
    }).DataTable();

    oc.init({ 'data': datasource });
}

function cambiarEstado_modulo(id, est) {

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
                url: "gestionit_controlador.php?op=activarydesactivar_modulo",
                method: "POST",
                data: {id: id, est: est},
                success: function (data) {
                    $('#modulo_data').DataTable().ajax.reload();
                }
            });
        }
    })
}

function cambiarEstado_menu(id, est) {

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
                url: "gestionit_controlador.php?op=activarydesactivar_menu",
                method: "POST",
                data: {id: id, est: est},
                success: function (data) {
                    $('#menu_data').DataTable().ajax.reload();
                }
            });
        }
    })
}

function mostrar_modulo(id_modulo= -1) {
    let isError = false;
    limpiar_modulo();

    $('#moduloModal').modal('show');

    $.ajax({
        url: "gestionit_controlador.php?op=mostrar_modulo",
        method: "POST",
        dataType: "json",
        data: {id_modulo: id_modulo},
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
            $.each(data.lista_menus, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#menu_id').append('<option name="" value="' + opt.id +'">' + opt.nombre.substr(0, 35) + '</option>');
            });

            //lista de seleccion
            $.each(data.lista_modulos, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#ruta').append('<option name="" value="' + opt +'">/' + opt.substr(0, 35) + '</option>');
            });

            if(id_modulo !== -1) {
                $('#moduloModal #nombre').val(data.nombre);
                $('#moduloModal #icono').val(data.icono).change();
                $('#ruta').val(data.ruta);
                $('#menu_id').val(data.menu_id);
                $('#estado').val(data.estatus);
                $('.modal-title').text("Editar Módulo");
                $('#id_modulo').val(id_modulo);
            }

        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function mostrar_menu(id_menu= -1) {
    let isError = false;
    limpiar_menu();

    $('#menuModal').modal('show');

    $.ajax({
        url: "gestionit_controlador.php?op=mostrar_menu",
        method: "POST",
        dataType: "json",
        data: {id_menu: id_menu},
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
            $('#menu_padre').append('<option name="" value="-1">Ninguno</option>');
            $.each(data.lista_menus, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                if (opt.id !== id_menu) {
                    $('#menu_padre').append('<option name="" value="' + opt.id +'">' + opt.nombre.substr(0, 35) + '</option>');
                }
            });

            //lista de seleccion
            $('#menu_hijo').append('<option name="" value="-1">Ninguno</option>');
            $.each(data.lista_menus, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                if (opt.id !== id_menu) {
                    $('#menu_hijo').append('<option name="" value="' + opt.id + '">' + opt.nombre.substr(0, 35) + '</option>');
                }
            });

            if(id_menu !== -1) {
                $('#menuModal #nombre').val(data.nombre);
                $('#orden').val(data.menu_orden);
                $('#menu_padre').val(data.menu_padre);
                $('#menu_hijo').val(data.menu_hijo);
                $('#menuModal #icono').val(data.icono).change();

                $('#estado').val(data.estatus);
                $('.modal-title').text("Editar Menú");
                $('#id_menu').val(id_menu);
            }

        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

//la funcion guardaryeditar(e); se llama cuando se da click al boton submit
function guardaryeditarModulo(e) {

    e.preventDefault(); //No se activará la acción predeterminada del evento
    let ruta = $('#modulo_form #ruta').val();
    const formData = new FormData($("#modulo_form")[0]);

    if (ruta !== "") {
        $.ajax({
            url: "gestionit_controlador.php?op=guardaryeditar_modulo",
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

                //verifica si el mensaje de insercion contiene error
                if(mensaje.includes('error')) {
                    return (false);
                } else {
                    $('#modulo_form')[0].reset();
                    $('#moduloModal').modal('hide');
                    $('#modulo_data').DataTable().ajax.reload();
                    limpiar_modulo();
                }
            }
        });
    } else {
        SweetAlertError('Debe seleccionar una RUTA para continuar.');
        return (false);
    }
}

//la funcion guardaryeditar(e); se llama cuando se da click al boton submit
function guardaryeditarMenu(e) {

    e.preventDefault(); //No se activará la acción predeterminada del evento
    const formData = new FormData($("#menu_form")[0]);

    $.ajax({
        url: "gestionit_controlador.php?op=guardaryeditar_menu",
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

            //verifica si el mensaje de insercion contiene error
            if(mensaje.includes('error')) {
                return (false);
            } else {
                cargar_datosGrafica();
                oc.init({ 'data': datasource });
                $('#menu_form')[0].reset();
                $('#menuModal').modal('hide');
                $('#menu_data').DataTable().ajax.reload();
                limpiar_menu();
            }
        }
    });
}

function guardarMenuSeleccionado(id, key, tipo) {
    let $tipo, $tabla;

    switch (tipo) {
        case 'modulo':
            $tabla = $('#modulo_data');
            $tipo = $('#menu'+key);
            break;
        case 'menu_padre':
            $tabla = $('#menu_data');
            $tipo = $('#menu_padre'+key);
            break;
        case 'menu_hijo':
            $tabla = $('#menu_data');
            $tipo = $('#menu_hijo'+key);
            break;
    }
    let tipo_value = $tipo.val();

    $.ajax({
        url: "gestionit_controlador.php?op=guardarseleccionado",
        type: "POST",
        dataType: "json",
        data: {id: id, tipo: tipo, tipo_value: tipo_value},
        error: function(e){
            SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            $tipo.val(tipo_value);
        },
        success: function (data) {
            let { icono, mensaje } = data;

            //verifica si el mensaje de insercion contiene error
            if(mensaje.includes('error')) {
                $tipo.val('');
                ToastSweetMenssage(icono, mensaje);
                return (false);
            } else {
                $tabla.DataTable().ajax.reload();
            }
        }
    });
}

function eliminar_modulo(id, modulo) {

    Swal.fire({
        // title: '¿Estas Seguro?',
        text: "¿Estas Seguro de Eliminar el módulo "+modulo+" ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "gestionit_controlador.php?op=eliminar_modulo",
                method: "POST",
                dataType: "json",
                data: {id: id},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    ToastSweetMenssage(data.icono, data.mensaje);
                    $('#modulo_data').DataTable().ajax.reload();
                }
            });
        }
    })
}

function eliminar_menu(id, menu) {

    Swal.fire({
        // title: '¿Estas Seguro?',
        text: "¿Estas Seguro de Eliminar el menú "+menu+" ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "gestionit_controlador.php?op=eliminar_menu",
                method: "POST",
                dataType: "json",
                data: {id: id},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    ToastSweetMenssage(data.icono, data.mensaje);
                    cargar_datosGrafica();
                    oc.init({ 'data': datasource });
                    $('#menu_data').DataTable().ajax.reload();
                }
            });
        }
    })
}

//Mostrar datos del usuario en la ventana modal del formularioS
init();






