var tabla;

//Función que se ejecuta al inicio
function init() {
    listar();

    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#vehiculo_form").on("submit", function (e) {
        guardaryeditar(e);
    })

    //cambia el titulo de la ventana modal cuando se da click al boton
    $("#add_button").click(function () {

        //habilita los campos cuando se agrega un registro nuevo ya que cuando se editaba un registro asociado entonces aparecia deshabilitado los campos
        $("#placa").attr('disabled', false);

        $(".modal-title").text("Agregar Vehiculo");

    });
}

/*funcion para limpiar formulario de modal*/
function limpiar() {
    $("#placa").val("");
    $('#modelo').val("");
    $('#capacidad').val("");
    $('#volumen').val("");
    $('#estado').val("");
    $('#id_vehiculo').val("");
}

//function listar
function listar() {
    let isError = false;
    tabla = $('#vehiculo_data').dataTable({

        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'vehiculos_controlador.php?op=listar',
                type: "get",
                dataType: "json",
                beforeSend: function () {
                    SweetAlertLoadingShow();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText, "Error!")
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
                url: "vehiculos_controlador.php?op=activarydesactivar",
                method: "POST",
                data: {id: id, est: est},
                success: function (data) {
                    $('#vehiculo_data').DataTable().ajax.reload();
                }
            });
        }
    })
}

function mostrar(id_vehiculo = -1) {
    let isError = false;
    limpiar();
    $('#vehiculoModal').modal('show');

    if(id_vehiculo !== -1)
    {
        $.ajax({
            url: "vehiculos_controlador.php?op=mostrar",
            method: "POST",
            dataType: "json",
            data: {id_vehiculo: id_vehiculo},
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            success: function (data) {

                $('#placa').val(data.placa);
                $("#placa").prop("disabled", true);
                $('#modelo').val(data.modelo);
                $('#capacidad').val(data.capacidad);
                $('#volumen').val(data.volumen);
                $('#estado').val(data.estado);
                $('.modal-title').text("Editar Vehiculo");
                $('#id_vehiculo').val(id_vehiculo);

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
    var formData = new FormData($("#vehiculo_form")[0]);
    $.ajax({
        url: "vehiculos_controlador.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (datos) {
            let { icono, mensaje } = datos;
            ToastSweetMenssage(icono, mensaje);

            //verifica si el mensaje de insercion contiene error
            if(mensaje.includes('error')) {
                return (false);
            } else {
                $('#vehiculo_form')[0].reset();
                $('#vehiculoModal').modal('hide');
                $('#vehiculo_data').DataTable().ajax.reload();
                limpiar();
            }

        }
    });
}

function eliminar(id, vehiculo) {

    Swal.fire({
        // title: '¿Estas Seguro?',
        text: "¿Estas Seguro de Eliminar el vehículo "+vehiculo+" ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "vehiculos_controlador.php?op=eliminar",
                method: "POST",
                dataType: "json",
                data: {id: id},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    console.log(e.responseText);
                },
                success: function (data) {
                    ToastSweetMenssage(data.icono, data.mensaje);
                    $('#vehiculo_data').DataTable().ajax.reload();
                }
            });
        }
    })
}


//Mostrar datos del usuario en la ventana modal del formularioS
init();






