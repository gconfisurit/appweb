let tabla;

let validator;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listar();
}

$(document).ready(function () {
    $.validator.setDefaults({
        submitHandler: function () {
            guardaryeditar();
        }
    });

    $("#objetivo_kpi").change(() => {
        tipo_objetivo_kpi($('#objetivo_kpi').val())
    });

    $("#obj_ventas_divisas").inputmask('currency',{
        "autoUnmask": true,
        radixPoint:",",
        groupSeparator: ".",
        allowMinus: false,
        prefix: '',
        digits: 2,
        digitsOptional: false,
        rightAlign: true,
        unmaskAsNumber: true
    });

    validaciones();
});

/*funcion para limpiar formulario de modal*/
function limpiar() {
    validator.resetForm();
    $('#supervisor').val("");
    $("#ruta").val("");
    $("#ruta").prop("readonly", true);
    $('#obj_ventas_kg').val("0");
    $('#nombre').val("");
    $('#obj_ventas_bul').val("0");
    $('#cedula').val("");
    $('#obj_ventas_und').val("0");
    $('#ubicacion').val("");
    $('#drop_size').val("0");
    $("#drop_size").prop("readonly", true);
    $('#clase').html("");
    $('#obj_clientes_captar').val("0");
    $('#obj_especial').val("0");
    $('#deposito').val("01");
    $("#deposito").prop("readonly", true);
    $('#logro_obj_especial').val("0");
    $('#frecuencia').val("");
    $('#tiempo_est_despacho').val("0");
    $('#obj_ventas_divisas').val("0");
    $('#obj_ava').val("");
    $('#objetivo_kpi').html("");
    $('#fotos_ava').val("");

    if ($("#supervisor").hasClass('is-valid')) {
        $('#supervisor').removeClass('is-valid');
    }
    if ($("#supervisor").hasClass('is-warning')) {
        $("#supervisor").removeClass('is-warning')
    }
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

//function listar
function listar() {
    let isError = false;
    tabla = $('#tabla').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'kpimanager_controlador.php?op=listar',
                type: "post",
                dataType: "json",
                data: {edv: "-"},
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
                    validarCantidadRegistrosTabla();
                }
            },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,//Por cada 10 registros hace una paginación
        //"order": [[0, "asc"]],//Ordenar (columna,orden)
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
                url: "kpimanager_controlador.php?op=activarydesactivar",
                method: "POST",
                dataType: "json",
                data: {id: id, est: est},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    SweetAlertSuccessLoading(data.mensaje);
                    $('#tabla').DataTable().ajax.reload();
                }
            });
        }
    })
}

function mostrar(edv) {
    let isError = false;
    $('#kpimanagerModal').modal('show');
    limpiar();

    $.ajax({
        url: "kpimanager_controlador.php?op=mostrar",
        type: "POST",
        dataType: "json",
        data: {edv: edv},
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {

            $('#clase').append('<option name="" value="">SELECCIONE UNA OPCION</option>');
            $.each(data.lista_clases_kpi, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#clase').append('<option name="" value="' + opt.clase +'">' + opt.clase.substr(0, 35) + '</option>');
            });

            $('#objetivo_kpi').append('<option name="" value="">SELECCIONE UNA OPCION</option>');
            $.each(data.lista_obj_kpi, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#objetivo_kpi').append('<option name="" value="' + opt.id +'">' + opt.descripcion.substr(0, 35) + '</option>');
            });

            $('#supervisor').val(data.coordinador.toUpperCase());
            $("#ruta").val(data.codvend);
            $("#ruta").prop("readonly", true);
            $('#obj_ventas_kg').val(data.obj_ventas_kg);
            $('#nombre').val(data.nombre);
            $('#obj_ventas_bul').val(data.obj_ventas_bul);
            //$('#cedula').val(data.cedula);
            $('#obj_ventas_und').val(data.obj_ventas_und);
            $('#ubicacion').val(data.ubicacion);
            $('#drop_size').val(data.obj_dropsize);
            $("#drop_size").prop("readonly", true);
            $('#clase').val(data.clase);
            //$('#obj_clientes_captar').val(data.obj_captar_clientes);
            $('#obj_especial').val(data.obj_especial);
            $('#deposito').val(data.deposito);
            $("#deposito").prop("readonly", true);
            $('#logro_obj_especial').val(data.obj_logro_especial);
            $('#frecuencia').val(data.frecuencia);
            $('#tiempo_est_despacho').val(data.tiempo_estimado_despacho);
            $('#obj_ventas_divisas').val(data.obj_ventas_divisas);
            $('#obj_ava').val(data.ava);
            $('#objetivo_kpi').val( (data.objetivo_kpi!=="0") ? data.objetivo_kpi : "" );
            $('#fotos_ava').val(data.ava_fotos);

            tipo_objetivo_kpi($('#objetivo_kpi').val());

            $("#supervisor").addClass(
                ($("#supervisor").val().length > 0 ? 'is-valid' : 'is-warning')
            );
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function guardaryeditar() {
    const formData = new FormData($("#edv_form")[0]);
    $.ajax({
        url: "kpimanager_controlador.php?op=guardar",
        type: "POST",
        dataType: "json",
        data: formData,
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
            $('#edv_form')[0].reset();
            $('#kpimanagerModal').modal('hide');
            $('#tabla').DataTable().ajax.reload();
            limpiar();
        }
    });
}

function tipo_objetivo_kpi(valor) {
    $('#obj_ventas_kg').val('0');
    $('#obj_ventas_bul').val('0');
    $('#obj_ventas_und').val('0');

    $('#obj_ventas_kg').prop("readonly", valor!=='1');
    $('#obj_ventas_bul').prop("readonly", valor!=='2');
    $('#obj_ventas_und').prop("readonly", valor!=='3');

    if (valor!=='1' && !$("#obj_ventas_kg").hasClass('bg-gray-light')) {
        $("#obj_ventas_kg").addClass('bg-gray-light');
    } else {
        $('#obj_ventas_kg').removeClass('bg-gray-light');
    }

    if (valor!=='2' && !$("#obj_ventas_bul").hasClass('bg-gray-light')) {
        $("#obj_ventas_bul").addClass('bg-gray-light');
    } else {
        $('#obj_ventas_bul').removeClass('bg-gray-light');
    }

    if (valor!=='3' && !$("#obj_ventas_und").hasClass('bg-gray-light')) {
        $("#obj_ventas_und").addClass('bg-gray-light');
    } else {
        $('#obj_ventas_und').removeClass('bg-gray-light');
    }


}

function validaciones() {
    validator = $('#edv_form').validate({
        rules: {
            obj_ventas_kg: {
                required: true,
                number: true,
                min: 0,
            },
            nombre: {
                required: true,
                minlength: 5
            },
            obj_ventas_bul: {
                required: true,
                number: true,
                min: 0,
            },
            obj_ventas_und: {
                required: true,
                number: true,
                min: 0,
            },
            ubicacion: {
                required: true,
                minlength: 5
            },
            clase: {
                required: true,
            },
            obj_especial: {
                required: true,
                number: true,
                min: 0,
            },
            logro_obj_especial: {
                required: true,
                number: true,
                min: 0,
            },
            frecuencia: {
                required: true,
            },
            tiempo_est_despacho: {
                required: true,
                number: true,
                min: 0,
            },
            obj_ventas_divisas: {
                // required: true,
                number: true,
                min: 0,
            },
            objetivo_kpi: {
                required: true,
            },
        },
        messages: {
            obj_ventas_kg: {
                required: "Campo requerido",
                number: "Ingrese sólo valores numéricos",
                min: "valor mínimo aceptable es 0"
            },
            nombre: {
                required: "Campo requerido",
                minlength: "El Campo debe contener al menos 5 caracteres"
            },
            obj_ventas_bul: {
                required: "Campo requerido",
                number: "Ingrese sólo valores numéricos",
                min: "valor mínimo aceptable es 0"
            },
            obj_ventas_und: {
                required: "Campo requerido",
                number: "Ingrese sólo valores numéricos",
                min: "valor mínimo aceptable es 0"
            },
            ubicacion: {
                required: "Campo requerido",
                minlength: "el Campo debe contener al menos 5 caracteres"
            },
            clase: {
                required: "Campo requerido",
            },
            obj_especial: {
                required: "Campo requerido",
                number: "Ingrese sólo valores numéricos",
                min: "valor mínimo aceptable es 0"
            },
            logro_obj_especial: {
                required: "Campo requerido",
                number: "Ingrese sólo valores numéricos",
                min: "valor mínimo aceptable es 0"
            },
            frecuencia: {
                required: "Campo requerido",
            },
            tiempo_est_despacho: {
                required: "Campo requerido",
                number: "Ingrese sólo valores numéricos",
                min: "valor mínimo aceptable es 0"
            },
            obj_ventas_divisas: {
                // required: "Campo requerido",
                number: "Ingrese sólo valores numéricos",
                min: "valor mínimo aceptable es 0"
            },
            objetivo_kpi: {
                required: "Campo requerido",
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

init();